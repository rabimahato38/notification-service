# Kubernetes manifests have moved

The manifests for the `notification` namespace now live in the GitLab project
`root/notification-deploy`. ArgoCD applies whatever is on its `main` branch.

This repo's pipeline builds the images and, in the `release_k3s` job, commits the new
image tag to that repo. To change how the app runs on k3s, edit the deploy repo.
