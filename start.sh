#!/bin/bash

# Start Minikube with Docker driver
minikube start --driver=docker

# Set Docker environment to Minikube's Docker daemon
eval $(minikube docker-env)

# Create namespace if not exists
kubectl apply --validate=false -f k8s/config/namespace.yaml

# Check if Helm release exists
RELEASE_NAME="personal-cinema"
NAMESPACE="personal-cinema"
if helm ls -n $NAMESPACE | grep -q $RELEASE_NAME; then
  echo "Helm release exists. Performing upgrade."
  helm upgrade $RELEASE_NAME k8s/helm -n $NAMESPACE
else
  echo "Helm release does not exist. Performing install."
  helm install $RELEASE_NAME k8s/helm -n $NAMESPACE
fi

# Forward ports for local access (optional)
kubectl port-forward --namespace personal-cinema service/admin-frontend 3001:80 &
kubectl port-forward --namespace personal-cinema service/backend 3000:80 &
kubectl port-forward --namespace personal-cinema service/cdn-backend 3002:80 &
kubectl port-forward --namespace personal-cinema service/frontend 3003:80 &

echo "Development environment is set up and running."
