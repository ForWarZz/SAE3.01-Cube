<?php

namespace App\Services\AI;

use Gemini\Data\FunctionDeclaration;
use Gemini\Data\Schema;
use Gemini\Data\Tool;
use Gemini\Enums\DataType;

class GeminiFunctionRegistry
{
    public static function getTools(): array
    {
        return [
            new Tool(
                functionDeclarations: [
                    self::getUserData(),
                    self::getUserOrders(),
                    self::getUserOrderDetail(),
                    self::getUserRegisteredBikes(),
                    self::getCartContent(),
                    self::searchArticles(),
                    self::getReferenceDetails(),
                    self::getReferenceAvailability(),
                    self::getArticleVariants(),
                    self::getSimilarArticles(),
                    self::getCompatibleAccessories(),
                    self::getCategories(),
                    self::getCurrentCategoryDetails(),
                ]
            ),
        ];
    }

    private static function getUserData(): FunctionDeclaration
    {
        return new FunctionDeclaration(
            name: 'get_user_data',
            description: 'Récupère les informations de l\'utilisateur connecté (nom, prénom, email, civilité, date de naissance, et tout autres données relatives au client). À utiliser pour personnaliser les réponses ou quand l\'utilisateur demande ses informations personnelles, où que sa demande nécessite d\'avoir des informations par rapport à son profil pour être plus précis. RGPD: Accessible uniquement si l\'utilisateur est connecté.',
            parameters: new Schema(
                type: DataType::OBJECT,
                properties: [
                    'include_addresses' => new Schema(
                        type: DataType::BOOLEAN,
                        description: 'Inclure les adresses enregistrées de l\'utilisateur (défaut: false)'
                    ),
                ]
            )
        );
    }

    private static function getUserOrders(): FunctionDeclaration
    {
        return new FunctionDeclaration(
            name: 'get_user_orders',
            description: 'Récupère la liste des commandes de l\'utilisateur connecté avec leurs statuts. À utiliser quand l\'utilisateur demande ses commandes, l\'historique d\'achats, ou veut connaître un statut de livraison. RGPD: Accessible uniquement si l\'utilisateur est connecté.',
            parameters: new Schema(
                type: DataType::OBJECT,
                properties: [
                    'limit' => new Schema(
                        type: DataType::INTEGER,
                        description: 'Nombre de commandes à récupérer (défaut: 10)'
                    ),
                    'include_details' => new Schema(
                        type: DataType::BOOLEAN,
                        description: 'Inclure les articles de chaque commande, l\'adresse de livraison, de facturation, le détail du magasin dans le cas d\'un click and collect (défaut: false)'
                    ),
                ]
            )
        );
    }

    private static function getUserOrderDetail(): FunctionDeclaration
    {
        return new FunctionDeclaration(
            name: 'get_user_order_detail',
            description: 'Récupère les détails complets d\'une commande spécifique de l\'utilisateur connecté (articles, prix, adresses, statut, suivi, mode de paiement, récapitulatif, et optionnellement les demandes de retour). À utiliser quand l\'utilisateur demande des détails sur une commande précise ou consulte une page de détail de commande. RGPD: Accessible uniquement pour les commandes de l\'utilisateur connecté.',
            parameters: new Schema(
                type: DataType::OBJECT,
                properties: [
                    'order_id' => new Schema(
                        type: DataType::INTEGER,
                        description: 'ID de la commande (clé primaire). Utiliser de préférence si disponible dans l\'URL ou le contexte.'
                    ),
                    'order_number' => new Schema(
                        type: DataType::STRING,
                        description: 'Numéro de commande, mélange de lettre et chiffre, en majuscule, de 9 caractères (ex: ZU1PMH0EN). Utiliser si order_id non disponible.'
                    ),
                    'include_returns' => new Schema(
                        type: DataType::BOOLEAN,
                        description: 'Inclure les demandes de retour associées à cette commande (défaut: false). Utiliser quand l\'utilisateur demande des informations sur les retours.'
                    ),
                ]
            )
        );
    }

    private static function getCartContent(): FunctionDeclaration
    {
        return new FunctionDeclaration(
            name: 'get_cart_content',
            description: 'Récupère le contenu du panier de l\'utilisateur ou visiteur actuel (articles, quantités, prix total, réductions). À utiliser quand l\'utilisateur pose des questions sur son panier, le contenu où qu\'il est nécessaire de connaître les articles dans le panier pour répondre à la demande.',
            parameters: new Schema(
                type: DataType::OBJECT,
                properties: []
            )
        );
    }

    private static function getUserRegisteredBikes(): FunctionDeclaration
    {
        return new FunctionDeclaration(
            name: 'get_user_registered_bikes',
            description: 'Récupère la liste des vélos enregistrés par l\'utilisateur connecté (numéro de série, date d\'achat, millésime, magasin). À utiliser quand l\'utilisateur demande ses vélos enregistrés, veut consulter un numéro de série, ou pose des questions sur l\'enregistrement de vélos. RGPD: Accessible uniquement si l\'utilisateur est connecté.',
            parameters: new Schema(
                type: DataType::OBJECT,
                properties: []
            )
        );
    }

    private static function searchArticles(): FunctionDeclaration
    {
        return new FunctionDeclaration(
            name: 'search_articles',
            description: 'Recherche des articles (vélos, accessoires) selon des critères variés. Utilise cette fonction quand l\'utilisateur cherche un produit. Tu as accès aux IDs des catégories, usages, matériaux dans les données de référence ci-dessus.',
            parameters: new Schema(
                type: DataType::OBJECT,
                properties: [
                    'query' => new Schema(
                        type: DataType::STRING,
                        description: 'Terme de recherche libre (nom, description, modèle)'
                    ),
                    'category_ids' => new Schema(
                        type: DataType::ARRAY,
                        description: 'IDs des catégories à filtrer (utilise les IDs de l\'arborescence fournie). Accepte plusieurs IDs pour élargir la recherche.',
                        items: new Schema(type: DataType::INTEGER)
                    ),
                    'usage_ids' => new Schema(
                        type: DataType::ARRAY,
                        description: 'IDs des usages/niveaux (débutant, expert, etc.)',
                        items: new Schema(type: DataType::INTEGER)
                    ),
                    'frame_material_ids' => new Schema(
                        type: DataType::ARRAY,
                        description: 'IDs des matériaux de cadre pour les vélos',
                        items: new Schema(type: DataType::INTEGER)
                    ),
                    'min_price' => new Schema(
                        type: DataType::NUMBER,
                        description: 'Prix minimum en euros'
                    ),
                    'max_price' => new Schema(
                        type: DataType::NUMBER,
                        description: 'Prix maximum en euros'
                    ),
                    'has_discount' => new Schema(
                        type: DataType::BOOLEAN,
                        description: 'Filtrer uniquement les articles en promotion'
                    ),
                    'is_bike' => new Schema(
                        type: DataType::BOOLEAN,
                        description: 'true = vélos uniquement, false = accessoires uniquement, null = tous',
                        nullable: true
                    ),
                    'is_ebike' => new Schema(
                        type: DataType::BOOLEAN,
                        description: 'true = vélos électriques uniquement, false = vélos musculaires uniquement',
                        nullable: true
                    ),
                    'limit' => new Schema(
                        type: DataType::INTEGER,
                        description: 'Nombre maximum de résultats (défaut: 10, max: 50)'
                    ),
                    'sort_by' => new Schema(
                        type: DataType::STRING,
                        description: 'Tri des résultats',
                        enum: ['relevance', 'price_asc', 'price_desc', 'sales', 'name_asc', 'name_desc']
                    ),
                ]
            )
        );
    }

    private static function getReferenceDetails(): FunctionDeclaration
    {
        return new FunctionDeclaration(
            name: 'get_reference_details',
            description: 'Récupère les détails complets d\'une référence spécifique d\'article (informations produit, caractéristiques, couleur, cadre, batterie, etc.). Utilise cette fonction quand l\'utilisateur consulte une fiche produit ou pose des questions sur une référence précise.',
            parameters: new Schema(
                type: DataType::OBJECT,
                properties: [
                    'reference_id' => new Schema(
                        type: DataType::INTEGER,
                        description: 'ID de la référence produit'
                    ),
                ],
                required: ['reference_id']
            )
        );
    }

    private static function getReferenceAvailability(): FunctionDeclaration
    {
        return new FunctionDeclaration(
            name: 'get_reference_availability',
            description: 'Récupère les disponibilités en ligne et en magasin d\'une référence par taille. À utiliser quand l\'utilisateur demande si un produit est disponible, en stock, ou dans quelle taille.',
            parameters: new Schema(
                type: DataType::OBJECT,
                properties: [
                    'reference_id' => new Schema(
                        type: DataType::INTEGER,
                        description: 'ID de la référence produit'
                    ),
                ],
                required: ['reference_id']
            )
        );
    }

    private static function getArticleVariants(): FunctionDeclaration
    {
        return new FunctionDeclaration(
            name: 'get_article_variants',
            description: 'Récupère toutes les variantes disponibles d\'un article (couleurs, cadres, batteries différentes). Utilise quand l\'utilisateur demande les options disponibles ou veut comparer les variantes.',
            parameters: new Schema(
                type: DataType::OBJECT,
                properties: [
                    'article_id' => new Schema(
                        type: DataType::INTEGER,
                        description: 'ID de l\'article'
                    ),
                ],
                required: ['article_id']
            )
        );
    }

    private static function getSimilarArticles(): FunctionDeclaration
    {
        return new FunctionDeclaration(
            name: 'get_similar_articles',
            description: 'Récupère les articles similaires à un article donné pour faire des recommandations. À utiliser pour suggérer des alternatives ou des produits similaires.',
            parameters: new Schema(
                type: DataType::OBJECT,
                properties: [
                    'article_id' => new Schema(
                        type: DataType::INTEGER,
                        description: 'ID de l\'article'
                    ),
                ],
                required: ['article_id']
            )
        );
    }

    private static function getCompatibleAccessories(): FunctionDeclaration
    {
        return new FunctionDeclaration(
            name: 'get_compatible_accessories',
            description: 'Récupère les accessoires compatibles avec un vélo spécifique. À utiliser quand l\'utilisateur demande quels accessoires peuvent aller avec un vélo.',
            parameters: new Schema(
                type: DataType::OBJECT,
                properties: [
                    'article_id' => new Schema(
                        type: DataType::INTEGER,
                        description: 'ID de l\'article vélo'
                    ),
                ],
                required: ['article_id']
            )
        );
    }

    private static function getCategories(): FunctionDeclaration
    {
        return new FunctionDeclaration(
            name: 'get_categories',
            description: 'Récupère l\'arbre complet des catégories de produits. Utilise pour connaître les catégories disponibles et leurs IDs pour les recherches.',
            parameters: new Schema(
                type: DataType::OBJECT,
                properties: []
            )
        );
    }

    private static function getCurrentCategoryDetails(): FunctionDeclaration
    {
        return new FunctionDeclaration(
            name: 'get_current_category',
            description: 'Récupère les détails de la catégorie actuelle consultée par l\'utilisateur, y compris son nom, et son positionnement dans l\'arborescence des catégories. Utilise cette fonction lorsque l\'utilisateur navigue dans une catégorie spécifique ou demande des informations sur la catégorie en cours.',
            parameters: new Schema(
                type: DataType::OBJECT,
                properties: [
                    'category_id' => new Schema(
                        type: DataType::INTEGER,
                        description: 'ID de la catégorie actuelle'
                    ),
                ],
                required: ['category_id']
            )
        );
    }
}
