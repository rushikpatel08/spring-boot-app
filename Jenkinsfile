pipeline {
    agent any

    environment {
        EC2_USER = 'ubuntu'
        EC2_HOST = 'ec2-3-92-255-138.compute-1.amazonaws.com'
        APP_PATH = '/home/ubuntu/spring-boot-app.jar'
        REPO_URL = 'https://github.com/rushikpatel08/spring-boot-app.git'
    }

    stages {
        stage('Checkout Code') {
            steps {
                git branch: 'main', url: "${REPO_URL}"
            }
        }

        stage('Build Spring Boot App') {
            steps {
                sh './mvnw clean package -DskipTests'
            }
        }

        stage('Deploy to EC2') {
            steps {
                sshagent(['ec2-key-pair']) {
                    sh '''
                    scp -o StrictHostKeyChecking=no target/*.jar ${EC2_USER}@${EC2_HOST}:${APP_PATH}
                    ssh ${EC2_USER}@${EC2_HOST} 'sudo systemctl restart springboot-app'
                    '''
                }
            }
        }
    }
}
