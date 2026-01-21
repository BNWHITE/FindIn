#!/bin/bash
# Wrapper script pour le serveur MCP FindIN
# S'assure d'utiliser le bon Python avec les dépendances installées

PYTHON_PATH="/Library/Frameworks/Python.framework/Versions/3.13/bin/python3"
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

exec "$PYTHON_PATH" "$SCRIPT_DIR/server.py" "$@"
