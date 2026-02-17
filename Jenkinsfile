pipeline {
    agent {
        dockerfile {
            filename 'Dockerfile'
        }
    }

    environment {
        APP_ENV = 'testing'
    }

    stages {
        stage('Check Tools') {
            steps {
                sh 'php -v'
                sh 'composer --version'
                sh 'npm -v'
            }
        }

        stage('Install Dependencies') {
            steps {
                sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
                sh 'npm install'
            }
        }

        stage('Prepare Environment') {
            steps {
                sh 'cp .env.testing .env'
                sh 'php artisan key:generate'
                sh 'npm run build'
            }
        }

        stage('Run Tests') {
            steps {
                sh 'php artisan test --parallel'
            }
        }

        stage('Lint & Code Style') {
            steps {
                sh './vendor/bin/pint --test'
            }
        }
    }

    post {
        always {
            script {
                try {
                    deleteDir()
                } catch (Exception e) {
                    echo "Could not clean workspace: ${e.message}"
                }
            }
        }
        failure {
            echo 'The pipeline failed. Check the logs above for the specific command that returned exit code 127.'
        }
    }
}
