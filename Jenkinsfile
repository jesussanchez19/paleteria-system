pipeline {
    agent {
        dockerfile {
            filename 'Dockerfile'
        }
    }

    environment {
        APP_ENV = 'testing'
        HOME = '.'
        NPM_CONFIG_CACHE = './.npm-cache'
    }

    stages {
        stage('Initialize') {
            steps {
                sh 'php -v'
                sh 'composer --version'
                sh 'npm -v'
                sh 'mkdir -p npm_cache'
            }
        }

        stage('Install Dependencies') {
            steps {
                sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
                sh 'npm install --cache ./npm_cache --quiet'
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

        /*
        stage('Lint & Code Style') {
            steps {
                sh './vendor/bin/pint --test'
            }
        }
        */
    }

    post {
        success {
            echo 'Build and tests passed successfully!'
        }
        failure {
            echo 'The pipeline failed. Ensure PHP, Composer, and NPM are installed on the Jenkins server.'
        }
    }
}
