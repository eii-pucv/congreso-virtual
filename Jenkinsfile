pipeline {
    agent any
    stages {
		stage("upload") {
			def inputFile = input message: 'Upload file', parameters: [file(name: '.env')]
			new hudson.FilePath(new File("$workspace/env.cfg")).copyFrom(inputFile)
			inputFile.delete()
		}
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
