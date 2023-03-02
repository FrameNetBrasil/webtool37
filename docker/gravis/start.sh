#! /usr/bin/env sh
set -e

cd /workspace

export PYTHONPATH=./app

chown -R unit:unit /workspace/app
chown -R unit:unit /workspace/files

echo "in start.sh"
unitd --no-daemon --control unix:/var/run/control.unit.sock