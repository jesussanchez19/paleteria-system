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
            cleanWs()
        }
        failure {
            echo 'The pipeline failed. Check the logs for more details.'
        }
    }
}
