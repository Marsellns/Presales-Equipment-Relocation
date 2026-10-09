#!/usr/bin/env bash
set -euo pipefail

repo_root="$(cd "$(dirname "$0")/.." && pwd)"
cd "$repo_root"
release_id="$(git rev-parse --short=12 HEAD)"
stage_root="$HOME/simaster-presales-staging/module-bundle-$release_id"

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

copy_manifest deploy/module-files.txt modules
copy_manifest deploy/integration-review-files.txt integration-review
/bin/cp -p -- deploy/MODULES-ONLY.md "$stage_root/README.md"
/bin/cp -p -- deploy/module-files.txt "$stage_root/module-files.txt"
/bin/cp -p -- deploy/integration-review-files.txt "$stage_root/integration-review-files.txt"
printf 'Module-only source staged at %s\n' "$stage_root"
printf 'No files were copied into public_html or the live application.\n'