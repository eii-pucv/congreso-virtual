pipeline {
    agent any
    stages {
        stage('Congreso setup (Fast setup/update)') {
			steps {
				sh	'''#!/bin/bash
					chmod 777 fast_setup_jenkins.sh
					./fast_setup_jenkins.sh
					'''
            }
        }
    }
}
