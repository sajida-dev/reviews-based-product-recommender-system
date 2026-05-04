<?php

return [
    // Qdrant config
    'qdrant_host' => env('QDRANT_HOST', 'http://localhost'),
    'qdrant_port' => env('QDRANT_PORT', 6333),
    'qdrant_api_key' => env('QDRANT_API_KEY'),
    'qdrant_collection' => env('QDRANT_COLLECTION', 'products'),

    // Embedding service (local python microservice or external)
    'embedding_service_url' => env('EMBEDDING_SERVICE_URL', 'http://localhost:8000'),
    'absa_service_url' => env('ABSA_SERVICE_URL', 'http://localhost:8001'),

    // Model version
    'model_version' => env('RECOMMENDATION_MODEL_VERSION', 'sentence-transformers/all-MiniLM-L6-v2'),

    // Security
    'api_rate_limit' => env('API_RATE_LIMIT', 60),
    'admin_ip_whitelist' => explode(',', env('ADMIN_IPS', '')),

    // Feature flags
    // Default false: use DB cosine similarity without a running Qdrant instance.
    'enable_qdrant' => env('ENABLE_QDRANT', false),
    'enable_local_faiss' => env('ENABLE_LOCAL_FAISS', false),

    // Cap candidates scanned for local (SQL-stored) similarity — tune per catalog size.
    'local_similarity_max_candidates' => (int) env('LOCAL_SIMILARITY_MAX_CANDIDATES', 500),

    // Cold-start / guest: prefer these category slugs (e.g. seeded "electronics" catalog).
    'cold_start_category_slugs' => array_values(array_filter(explode(',', (string) env('COLD_START_CATEGORY_SLUGS', 'electronics')))),
];
