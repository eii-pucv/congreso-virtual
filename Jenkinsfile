pipeline {
    agent any
    stages {
        stage('Congreso setup (Fast setup/update)') {
			steps {
				sh	'''#!/bin/bash
					chmod +x scripts/fast_setup_jenkins.sh
					cd scripts
					./fast_setup_jenkins.sh
					'''
            }
        }
    }
}
