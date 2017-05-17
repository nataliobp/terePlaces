#!/bin/bash
set -x 

#Setting ssh
openssl aes-256-cbc -K $encrypted_56a8236c23b2_key -iv $encrypted_56a8236c23b2_iv -in app/deploy/deploy_rsa.enc -out /tmp/deploy_rsa -d
eval "$(ssh-agent -s)"
chmod 600 /tmp/deploy_rsa
ssh-add /tmp/deploy_rsa

#copy artifact
tar -zcf artifact.tar.gz .
scp artifact.tar.gz natman@vps381493.ovh.net:/home/natman/terePlaces
ssh natman@vps381493.ovh.net 'tar -zxf /home/natman/terePlaces/artifact.tar.gz'
ssh natman@vps381493.ovh.net 'rm /home/natman/terePlaces/artifact.tar.gz'
ssh natman@vps381493.ovh.net 'mkdir -p /home/natman/terePlaces/app/config'
ssh natman@vps381493.ovh.net 'cp /home/natman/config.yml /home/natman/terePlaces/app/config/config.yml'

#get up docker
ssh natman@vps381493.ovh.net 'docker-compose -f /home/natman/terePlaces/docker-compose.yml stop'
ssh natman@vps381493.ovh.net 'docker system prune -f'
ssh natman@vps381493.ovh.net 'docker-compose  -f /home/natman/terePlaces/docker-compose.yml up -d'