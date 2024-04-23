def buildDir = 'build/'
pipeline {
    agent any
    
    options {
        buildDiscarder logRotator(artifactDaysToKeepStr: '', artifactNumToKeepStr: '5', daysToKeepStr: '', numToKeepStr: '5')
        disableConcurrentBuilds()
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }
        stage('Prepare') {
            steps {
                // remove build directories
                sh "rm -rf ${buildDir}api"
                sh "rm -rf ${buildDir}code-browser"
                sh "rm -rf ${buildDir}coverage"
                sh "rm -rf ${buildDir}logs"
                sh "rm -rf ${buildDir}pdepend"
                // create build directories
                sh "mkdir -p ${buildDir}api"
                sh "mkdir -p ${buildDir}code-browser"
                sh "mkdir -p ${buildDir}coverage"
                sh "mkdir -p ${buildDir}logs"
                sh "mkdir -p ${buildDir}pdepend"
                // set permission
                sh "chmod -R 777 ./storage/"
                sh "chmod -R 777 ./bootstrap/cache/"
                // Remove vendor
                sh "rm -rf vendor"
                // Remove composer.lock
                sh "rm composer.lock"
                // Database for test with Sqlite
                sh "mkdir -p database"
                sh "touch database/database.sqlite"
            }
        }
        stage('Environment') {
            steps {
                // set environment
                sh 'mv .env.example .env'
                sh 'rm -f .env.*'
            }
        }
        stage('for the fix branch') {
            when {
                branch "fix-*"
            }
            steps {
                sh '''
                  cat README.md 
                '''
            }
        }
        stage('for the PR') {
            when {
                branch "PR-*"
            }
            steps {
                echo 'this only runs for the PRs'
            }
        }
        stage('Install Dependencies') {
            steps {
                sh 'php --version'
                sh 'composer install --optimize-autoloader --no-ansi --no-interaction --ignore-platform-req=ext-zip --ignore-platform-req=ext-bcmath'
                sh 'composer --version'
                // Cache clear
                sh "php artisan cache:clear"
                sh "php artisan clear-compiled"
                // Key aplilcation generate
                sh 'php artisan key:generate'
                // Change mode coverage
                sh 'php -dxdebug.mode=coverage'
            }
        }
        stage("PHPUnit Test") {
            steps {
                sh "./vendor/bin/phpunit --coverage-html ${buildDir}coverage --coverage-clover ${buildDir}coverage/clover.xml --coverage-crap4j ${buildDir}logs/crap4j.xml --log-junit ${buildDir}logs/junit.xml"
            }
        }
        stage("Static code analysis larastan") {
            steps {
                sh "./vendor/bin/phpstan analyse --memory-limit=2G"
            }
        }
        stage("Built on top of PHP-CS-Fixer PSR12 with pint") {
            steps {
                sh "./vendor/bin/pint --preset psr12"
            }
        }
        stage("Analyze the code quality with Php Insights") {
            steps {
                sh "./vendor/bin/phpinsights --no-interaction --min-quality=80 --min-complexity=80 --min-architecture=70 --min-style=70 --disable-security-check"
            }
        }
        stage('Publish Reporting') {
            steps {
                junit "${buildDir}logs/junit.xml"
                publishHTML (target: [
                    allowMissing: false,
                    alwaysLinkToLastBuild: true,
                    keepAll: true,
                    reportDir: "${buildDir}coverage",
                    reportFiles: 'index.html',
                    reportName: "PHP Code Coverage"
                ])
                recordIssues enabledForFailure: true, tool: checkStyle(pattern: "${buildDir}logs/checkstyle.xml")
                recordIssues enabledForFailure: true, tool: pmdParser(pattern: "${buildDir}logs/pmd.xml")
                recordIssues enabledForFailure: true, tool: cpd(pattern: "${buildDir}logs/pmd-cpd.xml")
                sh "echo '<html><head><title>PHP Dependency</title></head><body></body><img src=\"dependencies.svg\"></br><img src=\"overview-pyramid.svg\"></html>' > ${buildDir}pdepend/index.html"
                publishHTML (target: [
                    allowMissing: false,
                    alwaysLinkToLastBuild: true,
                    keepAll: true,
                    reportDir: "${buildDir}pdepend",
                    reportFiles: 'index.html',
                    reportName: "PHP Dependencies"
                ])
            }
        }
    }
}