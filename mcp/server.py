#!/usr/bin/env python3
"""
FindIN MCP Server - Serveur MCP pour analyser et améliorer le projet FindIN avec XAMPP
"""

import asyncio
import json
import sys
from pathlib import Path
from typing import Any, Optional
import mysql.connector
from mysql.connector import Error

# Ajouter le support MCP
try:
    from mcp.server import Server, NotificationOptions
    from mcp.server.models import InitializationOptions
    import mcp.server.stdio
    import mcp.types as types
except ImportError:
    print("Erreur: le package 'mcp' n'est pas installé. Installez-le avec: pip install mcp", file=sys.stderr)
    sys.exit(1)

# Configuration XAMPP par défaut
DB_CONFIG = {
    'host': 'localhost',
    'port': 3306,
    'database': 'gestion_competences',
    'user': 'root',
    'password': ''
}

# Chemin du projet
PROJECT_ROOT = Path(__file__).parent.parent

class FindINMCPServer:
    def __init__(self):
        self.server = Server("findin-mcp")
        self.db_connection: Optional[Any] = None
        
        # Enregistrer les gestionnaires
        self.setup_handlers()
    
    def get_db_connection(self):
        """Obtenir une connexion à la base de données MySQL XAMPP"""
        if self.db_connection is None or not self.db_connection.is_connected():
            try:
                self.db_connection = mysql.connector.connect(**DB_CONFIG)
            except Error as e:
                raise Exception(f"Erreur de connexion MySQL: {e}")
        return self.db_connection
    
    def setup_handlers(self):
        """Configurer les gestionnaires de requêtes MCP"""
        
        @self.server.list_tools()
        async def handle_list_tools() -> list[types.Tool]:
            """Liste tous les outils disponibles"""
            return [
                types.Tool(
                    name="query_database",
                    description="Exécute une requête SQL SELECT sur la base de données FindIN (lecture seule)",
                    inputSchema={
                        "type": "object",
                        "properties": {
                            "query": {
                                "type": "string",
                                "description": "Requête SQL SELECT à exécuter"
                            }
                        },
                        "required": ["query"]
                    }
                ),
                types.Tool(
                    name="get_table_structure",
                    description="Obtient la structure d'une table (colonnes, types, clés)",
                    inputSchema={
                        "type": "object",
                        "properties": {
                            "table_name": {
                                "type": "string",
                                "description": "Nom de la table à analyser"
                            }
                        },
                        "required": ["table_name"]
                    }
                ),
                types.Tool(
                    name="list_tables",
                    description="Liste toutes les tables de la base de données",
                    inputSchema={
                        "type": "object",
                        "properties": {}
                    }
                ),
                types.Tool(
                    name="analyze_php_file",
                    description="Analyse un fichier PHP du projet (structure, classes, fonctions)",
                    inputSchema={
                        "type": "object",
                        "properties": {
                            "file_path": {
                                "type": "string",
                                "description": "Chemin relatif du fichier PHP depuis la racine du projet"
                            }
                        },
                        "required": ["file_path"]
                    }
                ),
                types.Tool(
                    name="get_project_stats",
                    description="Obtient des statistiques sur le projet (fichiers, lignes de code, etc.)",
                    inputSchema={
                        "type": "object",
                        "properties": {}
                    }
                ),
                types.Tool(
                    name="check_database_consistency",
                    description="Vérifie la cohérence de la base de données (tables manquantes, relations)",
                    inputSchema={
                        "type": "object",
                        "properties": {}
                    }
                ),
                types.Tool(
                    name="get_user_competences",
                    description="Obtient les compétences d'un utilisateur spécifique",
                    inputSchema={
                        "type": "object",
                        "properties": {
                            "user_id": {
                                "type": "string",
                                "description": "ID de l'utilisateur"
                            }
                        },
                        "required": ["user_id"]
                    }
                ),
                types.Tool(
                    name="search_code_pattern",
                    description="Recherche un pattern dans les fichiers PHP du projet",
                    inputSchema={
                        "type": "object",
                        "properties": {
                            "pattern": {
                                "type": "string",
                                "description": "Pattern regex ou texte à rechercher"
                            },
                            "file_extension": {
                                "type": "string",
                                "description": "Extension de fichier (par défaut: php)",
                                "default": "php"
                            }
                        },
                        "required": ["pattern"]
                    }
                )
            ]
        
        @self.server.call_tool()
        async def handle_call_tool(
            name: str, arguments: dict | None
        ) -> list[types.TextContent | types.ImageContent | types.EmbeddedResource]:
            """Gère les appels d'outils"""
            
            if name == "query_database":
                return await self.query_database(arguments.get("query", ""))
            
            elif name == "get_table_structure":
                return await self.get_table_structure(arguments.get("table_name", ""))
            
            elif name == "list_tables":
                return await self.list_tables()
            
            elif name == "analyze_php_file":
                return await self.analyze_php_file(arguments.get("file_path", ""))
            
            elif name == "get_project_stats":
                return await self.get_project_stats()
            
            elif name == "check_database_consistency":
                return await self.check_database_consistency()
            
            elif name == "get_user_competences":
                return await self.get_user_competences(arguments.get("user_id", ""))
            
            elif name == "search_code_pattern":
                return await self.search_code_pattern(
                    arguments.get("pattern", ""),
                    arguments.get("file_extension", "php")
                )
            
            else:
                raise ValueError(f"Outil inconnu: {name}")
    
    async def query_database(self, query: str) -> list[types.TextContent]:
        """Exécute une requête SQL SELECT"""
        if not query.strip().upper().startswith("SELECT"):
            return [types.TextContent(
                type="text",
                text="Erreur: Seules les requêtes SELECT sont autorisées"
            )]
        
        try:
            conn = self.get_db_connection()
            cursor = conn.cursor(dictionary=True)
            cursor.execute(query)
            results = cursor.fetchall()
            cursor.close()
            
            return [types.TextContent(
                type="text",
                text=json.dumps(results, indent=2, default=str, ensure_ascii=False)
            )]
        except Error as e:
            return [types.TextContent(
                type="text",
                text=f"Erreur SQL: {e}"
            )]
    
    async def get_table_structure(self, table_name: str) -> list[types.TextContent]:
        """Obtient la structure d'une table"""
        try:
            conn = self.get_db_connection()
            cursor = conn.cursor(dictionary=True)
            cursor.execute(f"DESCRIBE {table_name}")
            structure = cursor.fetchall()
            cursor.close()
            
            return [types.TextContent(
                type="text",
                text=f"Structure de la table '{table_name}':\n\n" + 
                     json.dumps(structure, indent=2, ensure_ascii=False)
            )]
        except Error as e:
            return [types.TextContent(
                type="text",
                text=f"Erreur: {e}"
            )]
    
    async def list_tables(self) -> list[types.TextContent]:
        """Liste toutes les tables"""
        try:
            conn = self.get_db_connection()
            cursor = conn.cursor()
            cursor.execute("SHOW TABLES")
            tables = [table[0] for table in cursor.fetchall()]
            cursor.close()
            
            return [types.TextContent(
                type="text",
                text=f"Tables dans la base de données '{DB_CONFIG['database']}':\n\n" +
                     "\n".join(f"- {table}" for table in tables)
            )]
        except Error as e:
            return [types.TextContent(
                type="text",
                text=f"Erreur: {e}"
            )]
    
    async def analyze_php_file(self, file_path: str) -> list[types.TextContent]:
        """Analyse un fichier PHP"""
        full_path = PROJECT_ROOT / file_path
        
        if not full_path.exists():
            return [types.TextContent(
                type="text",
                text=f"Erreur: Fichier non trouvé: {file_path}"
            )]
        
        try:
            content = full_path.read_text(encoding='utf-8')
            
            # Analyse basique
            lines = content.split('\n')
            num_lines = len(lines)
            
            # Recherche des classes
            classes = []
            functions = []
            for i, line in enumerate(lines, 1):
                if 'class ' in line:
                    classes.append(f"Ligne {i}: {line.strip()}")
                if 'function ' in line and 'public' in line or 'private' in line or 'protected' in line:
                    functions.append(f"Ligne {i}: {line.strip()}")
            
            result = f"Analyse de {file_path}:\n\n"
            result += f"Nombre de lignes: {num_lines}\n"
            result += f"Nombre de classes: {len(classes)}\n"
            result += f"Nombre de méthodes: {len(functions)}\n\n"
            
            if classes:
                result += "Classes trouvées:\n" + "\n".join(classes) + "\n\n"
            
            if functions:
                result += "Méthodes trouvées (échantillon):\n"
                result += "\n".join(functions[:10])
                if len(functions) > 10:
                    result += f"\n... et {len(functions) - 10} autres"
            
            return [types.TextContent(type="text", text=result)]
        
        except Exception as e:
            return [types.TextContent(
                type="text",
                text=f"Erreur lors de l'analyse: {e}"
            )]
    
    async def get_project_stats(self) -> list[types.TextContent]:
        """Obtient des statistiques sur le projet"""
        php_files = list(PROJECT_ROOT.rglob("*.php"))
        total_lines = 0
        
        for file in php_files:
            try:
                total_lines += len(file.read_text(encoding='utf-8').split('\n'))
            except:
                pass
        
        result = f"Statistiques du projet FindIN:\n\n"
        result += f"Fichiers PHP: {len(php_files)}\n"
        result += f"Lignes de code totales: {total_lines}\n"
        result += f"Moyenne par fichier: {total_lines // len(php_files) if php_files else 0}\n"
        
        return [types.TextContent(type="text", text=result)]
    
    async def check_database_consistency(self) -> list[types.TextContent]:
        """Vérifie la cohérence de la base de données"""
        try:
            conn = self.get_db_connection()
            cursor = conn.cursor()
            
            # Tables attendues
            expected_tables = [
                'utilisateurs', 'competences', 'competences_utilisateurs',
                'departements', 'projets', 'users', 'user_competences'
            ]
            
            cursor.execute("SHOW TABLES")
            existing_tables = [table[0] for table in cursor.fetchall()]
            
            missing = set(expected_tables) - set(existing_tables)
            extra = set(existing_tables) - set(expected_tables)
            
            result = "Vérification de la cohérence de la base de données:\n\n"
            result += f"Tables présentes: {len(existing_tables)}\n"
            
            if missing:
                result += f"\n⚠️ Tables manquantes:\n" + "\n".join(f"  - {t}" for t in missing)
            else:
                result += "\n✅ Toutes les tables attendues sont présentes"
            
            if extra:
                result += f"\n\nℹ️ Tables supplémentaires:\n" + "\n".join(f"  - {t}" for t in extra)
            
            cursor.close()
            return [types.TextContent(type="text", text=result)]
        
        except Error as e:
            return [types.TextContent(
                type="text",
                text=f"Erreur: {e}"
            )]
    
    async def get_user_competences(self, user_id: str) -> list[types.TextContent]:
        """Obtient les compétences d'un utilisateur"""
        query = """
            SELECT u.prenom, u.nom, u.email, c.nom as competence, 
                   uc.niveau_declare, uc.niveau_valide
            FROM user_competences uc
            JOIN users u ON uc.user_id = u.id
            JOIN competences c ON uc.competence_id = c.id
            WHERE u.id = %s OR u.id_utilisateur = %s
        """
        
        try:
            conn = self.get_db_connection()
            cursor = conn.cursor(dictionary=True)
            cursor.execute(query, (user_id, user_id))
            results = cursor.fetchall()
            cursor.close()
            
            if not results:
                return [types.TextContent(
                    type="text",
                    text=f"Aucune compétence trouvée pour l'utilisateur {user_id}"
                )]
            
            return [types.TextContent(
                type="text",
                text=json.dumps(results, indent=2, ensure_ascii=False)
            )]
        except Error as e:
            return [types.TextContent(
                type="text",
                text=f"Erreur SQL: {e}"
            )]
    
    async def search_code_pattern(self, pattern: str, file_extension: str = "php") -> list[types.TextContent]:
        """Recherche un pattern dans le code"""
        import re
        
        files = list(PROJECT_ROOT.rglob(f"*.{file_extension}"))
        results = []
        
        for file in files:
            try:
                content = file.read_text(encoding='utf-8')
                lines = content.split('\n')
                
                for i, line in enumerate(lines, 1):
                    if re.search(pattern, line, re.IGNORECASE):
                        relative_path = file.relative_to(PROJECT_ROOT)
                        results.append(f"{relative_path}:{i}: {line.strip()}")
            except:
                pass
        
        if not results:
            return [types.TextContent(
                type="text",
                text=f"Aucune correspondance trouvée pour le pattern: {pattern}"
            )]
        
        result_text = f"Résultats pour '{pattern}' ({len(results)} correspondances):\n\n"
        result_text += "\n".join(results[:50])
        
        if len(results) > 50:
            result_text += f"\n\n... et {len(results) - 50} autres correspondances"
        
        return [types.TextContent(type="text", text=result_text)]

async def main():
    """Point d'entrée principal"""
    server_instance = FindINMCPServer()
    
    async with mcp.server.stdio.stdio_server() as (read_stream, write_stream):
        await server_instance.server.run(
            read_stream,
            write_stream,
            InitializationOptions(
                server_name="findin-mcp",
                server_version="1.0.0",
                capabilities=server_instance.server.get_capabilities(
                    notification_options=NotificationOptions(),
                    experimental_capabilities={},
                ),
            ),
        )

if __name__ == "__main__":
    asyncio.run(main())
