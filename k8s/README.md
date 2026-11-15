# Kubernetes deployment (k3s, namespace `notification`)

Apply in file order. Two Secrets are created by hand and are not in Git:

```sh
kubectl apply -f 00-namespace.yaml -f 10-config.yaml
kubectl -n notification create secret docker-registry gitlab-registry \
  --docker-server=172.31.34.196:5050 --docker-username=<deploy token user> --docker-password=<deploy token>
kubectl -n notification create secret generic app-secrets \
  --from-literal=APP_KEY=<base64 key> --from-literal=DB_PASSWORD=<password>
kubectl apply -f 20-postgres.yaml
kubectl apply -f 30-migrate-job.yaml && kubectl -n notification wait --for=condition=complete job/migrate
kubectl apply -f 40-app.yaml
```

Node requirement: the AppArmor profile in `node/fable-c1-container.apparmor` must be
loaded on every node that runs these pods (today: w1):

```sh
sudo install -m 644 fable-c1-container.apparmor /etc/apparmor.d/fable-c1-container
sudo apparmor_parser -r /etc/apparmor.d/fable-c1-container
```

Without it the pods stay in `CreateContainerError` (profile not found). It is a copy
of containerd's default profile that also lets processes inside one container signal
each other, which Postgres, PHP-FPM and nginx need.

Access: `ssh -L 8080:172.31.11.8:80 <cp1>` then http://notification.localhost:8080

The database is a single replica on node-local storage: no failover, and deleting the
PVC deletes the data.
