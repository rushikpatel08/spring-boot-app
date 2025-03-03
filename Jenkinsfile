pipeline {
    agent any

    environment {
        AWS_REGION = 'us-east-1'
        S3_BUCKET = 'rushik-first-s3bucket'
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'master', url: 'https://github.com/rushikpatel08/spring-boot-app.git'
            }
        }

        stage('Build Spring Boot App') {
            steps {
                sh 'mvn clean package'
            }
        }

        stage('Build Angular App') {
            steps {
                sh 'cd angular-app && npm install && npm run build'
            }
        }

        stage('Deploy to S3') {
            steps {
                sh 'aws s3 sync angular-app/dist/angular-app s3://${S3_BUCKET} --delete'
            }
        }

        stage('Deploy to EC2') {
            steps {
                sshagent(['your-ec2-ssh-key']) {
                    sh '''
                        ssh -o StrictHostKeyChecking=no ec2-user@ec2-18-212-27-10.compute-1.amazonaws.com <<EOF
                        cd /home/ec2-user/app
                        git pull origin master
                        mvn spring-boot:run &
                        EOF
                    '''
                }
            }
        }
    }
}
