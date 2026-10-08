pipeline {
    agent any

    options {
        disableConcurrentBuilds()
        timestamps()
    }

    stages {
        stage('Build PHP 8.2 test image') {
            steps {
                powershell '''
                    $ErrorActionPreference = 'Stop'
                    docker version
                    docker build --pull -f ci/Dockerfile -t simaster-selected-ci:$env:BUILD_NUMBER .
                    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
                '''
            }
        }

        stage('Laravel tests') {
            steps {
                powershell '''
                    $ErrorActionPreference = 'Stop'
                    docker run --rm simaster-selected-ci:$env:BUILD_NUMBER php artisan test
                    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
                '''
            }
        }

        stage('Equipment Relocation asset tests') {
            steps {
                powershell '''
                    $ErrorActionPreference = 'Stop'
                    docker run --rm -v "$($env:WORKSPACE):/app:ro" -w /app node:22-alpine node --test tests/equipment-relocation-inventory.test.cjs
                    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
                '''
            }
        }
    }

    post {
        always {
            powershell '''
                if ($env:BUILD_NUMBER) {
                    docker image rm simaster-selected-ci:$env:BUILD_NUMBER 2>$null
                }
                exit 0
            '''
        }
    }
}
