pipeline {
    agent any

    environment {
        EC2_USER = 'ec2-user'
        EC2_HOST = 'ec2-3-92-255-138.compute-1.amazonaws.com'
        APP_PATH = '/home/ec2-user/spring-boot-app.jar'
        REPO_URL = 'https://github.com/rushikpatel08/spring-boot-app.git'
    }

    stages {
        stage('Checkout Code') {
            steps {
                git branch: 'master', url: "${REPO_URL}"
            }
        }

        stage('Build Spring Boot App') {
            steps {
                sh 'chmod +x mvnw'  
                sh './mvnw clean package -DskipTests'
            }
        }

        stage('Deploy to EC2') {
            steps {
                sshagent(['ec2-user']) {
                    sh """
                        echo 'Stopping existing application...'
                        ssh -o StrictHostKeyChecking=no $EC2_USER@$EC2_HOST 'sudo pkill -f spring-boot-app.jar || echo "No process found"'
                        
                        echo 'Uploading new application...'
                        scp -o StrictHostKeyChecking=no target/*.jar $EC2_USER@$EC2_HOST:$APP_PATH
                        
                        echo 'Starting new application...'
                        ssh -o StrictHostKeyChecking=no $EC2_USER@$EC2_HOST 'nohup java -jar $APP_PATH > app.log 2>&1 &'
                    """
                }
            }
        }
    }
}
