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
          sudo docker info
          sudo docker version
          sudo docker compose version
          curl --version
          jq --version
        '''
      }
    }
    stage("Prune docker data") {
      steps {
        sh 'sudo docker system prune -a --volumes -f'
      }
    }
    stage('Start container') {
      steps {
        sh 'sudo docker compose up -d --no-color --wait'
        sh 'sudo docker compose ps'
      }
    }
    stage("Run tests against the container") {
      steps {
        sh 'curl http://localhost:8080/api/products?sort='
      }
    }
  }
  post {
    always {
      sh 'sudo docker compose down --remove-orphans -v'
      sh 'sudo docker compose ps'
    }
  }
}