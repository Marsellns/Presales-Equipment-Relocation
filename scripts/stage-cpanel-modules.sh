#!/usr/bin/env bash
set -euo pipefail

repo_root="$(cd "$(dirname "$0")/.." && pwd)"
cd "$repo_root"
release_id="$(git rev-parse --short=12 HEAD)"
stage_root="$HOME/simaster-presales-staging/module-bundle-$release_id"
target_root="$HOME/laravel-core"

copy_manifest() {
    local manifest="$1"
    local bucket="$2"
    local relative destination

    while IFS= read -r relative || [[ -n "$relative" ]]; do
        [[ -n "$relative" ]] || continue
        case "$relative" in
            /*|../*|*/../*|*/..)
                echo "Unsafe manifest path: $relative" >&2
                exit 1
                ;;
        esac
        [[ -f "$relative" ]] || { echo "Missing source file: $relative" >&2; exit 1; }
        destination="$stage_root/$bucket/$relative"
        /bin/mkdir -p "$(dirname "$destination")"
        /bin/cp -p -- "$relative" "$destination"
    done < "$manifest"
}

compare_manifest() {
    local manifest="$1"
    local bucket="$2"
    local relative

    while IFS= read -r relative || [[ -n "$relative" ]]; do
        [[ -n "$relative" ]] || continue
        if [[ ! -f "$target_root/$relative" ]]; then
            printf 'MISSING %s %s\n' "$bucket" "$relative"
        elif cmp -s "$relative" "$target_root/$relative"; then
            printf 'SAME %s %s\n' "$bucket" "$relative"
        else
            printf 'DIFFERENT %s %s\n' "$bucket" "$relative"
        fi
    done < "$manifest"
}

copy_manifest deploy/module-files.txt modules
copy_manifest deploy/integration-review-files.txt integration-review
/bin/cp -p -- deploy/MODULES-ONLY.md "$stage_root/README.md"
/bin/cp -p -- deploy/module-files.txt "$stage_root/module-files.txt"
/bin/cp -p -- deploy/integration-review-files.txt "$stage_root/integration-review-files.txt"

report="$stage_root/target-comparison.txt"
if [[ -f "$target_root/artisan" && -f "$target_root/composer.json" ]]; then
    {
        printf 'Source commit: %s\n' "$release_id"
        printf 'Target: %s\n' "$target_root"
        compare_manifest deploy/module-files.txt modules
        compare_manifest deploy/integration-review-files.txt integration-review
    } > "$report"
    printf 'Module-only source staged at %s\n' "$stage_root"
    printf 'Read-only comparison with %s saved to %s\n' "$target_root" "$report"
else
    printf 'Target %s was not found with artisan and composer.json. No live files were touched.\n' "$target_root" > "$report"
    printf 'Module-only source staged at %s; target path not found.\n' "$stage_root"
fi
printf 'No files were copied into laravel-core or public_html.\n'