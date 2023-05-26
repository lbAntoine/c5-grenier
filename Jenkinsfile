pipeline {
    agent { dockerfile true }
    stages {
        // stage('Build') {
        //     steps {
        //       sh 'docker compose up -d'
        //     }
        // }
        stage('Test') {
            steps {
              sh 'echo "C\'est le build test"'
            }
        }
    }
}