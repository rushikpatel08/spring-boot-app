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

        stage('Test SSH Connection') {
    steps {
        script {
            def sshCommand = "ssh -v -o StrictHostKeyChecking=no ec2-user@ec2-3-92-255-138.compute-1.amazonaws.com 'echo SSH connection successful'"
            def status = sh(script: sshCommand, returnStatus: true)
            if (status != 0) {
                error "SSH connection failed!"
            }
        }
    }
}

        stage('Deploy to EC2') {
            steps {
                sshagent(['ec2-key-pair']) {
                    sh "scp -o StrictHostKeyChecking=no target/springboot_aws.jar ec2-user@ec2-3-92-255-138.compute-1.amazonaws.com:/home/ec2-user/"
            sh "ssh -o StrictHostKeyChecking=no ec2-user@ec2-3-92-255-138.compute-1.amazonaws.com 'nohup java -jar /home/ec2-user/springboot_aws.jar > /dev/null 2>&1 &'"
                }
            }
        }
    }
}
