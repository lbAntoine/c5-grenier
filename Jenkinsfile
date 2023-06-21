// pipeline {
//     agent { dockerfile true }
//     stages {
//         // stage('Build') {
//         //     steps {
//         //       sh 'docker compose up -d'
//         //     }
//         // }
//         stage('Test') {
//             steps {
//               sh 'echo "C\'est le build test"'
//             }
//         }
//     }
// }
pipeline {
  agent any
  stages {
    stage("Verify tooling") {
      steps {
        sh '''
          whoami
          docker info
          docker version
          docker compose version
          curl --version
          jq --version
        '''
      }
    }
    stage("Prune docker data") {
      steps {
        sh 'docker system prune -a --volumes -f'
      }
    }
    stage('Start container') {
      steps {
        sh 'docker compose up -d --no-color --wait'
        sh 'docker compose ps'
      }
    }
    stage("Run tests against the container") {
      steps {
        sh 'curl http://localhost:9999/api/products?sort='
      }
    }
  }
  post {
    always {
      sh 'docker compose down --remove-orphans -v'
      sh 'docker compose ps'
    }
  }
}