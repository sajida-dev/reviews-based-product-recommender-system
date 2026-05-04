# 📊 COMPREHENSIVE SYSTEM AUDIT REPORT

## AI-Powered Product Recommendation System (Laravel + Vue.js)

**Audit Date**: May 2026  
**Status**: Production-Ready Core | ML Integration Pending

---

## ⚠️ EXECUTIVE SUMMARY

### Strengths ✅

- **Solid Foundation**: Well-architected Laravel 11 with Service Layer pattern
- **Best Practices**: Proper use of Models, Migrations, FormRequests, Middleware
- **Security**: Fortify integration, 2FA, email verification, CSRF protection
- **Performance**: Soft deletes, proper indexes, relationship loading
- **Code Organization**: Services separate business logic from controllers
- **Queue System**: Async processing for emails and notifications
- **Async Architecture**: Events + Listeners for low-stock alerts

### Critical Gaps ❌

1. **No Vector/Embedding Support**: Missing core ML integration (Qdrant/FAISS)
2. **No Aspect-Based Sentiment Analysis (ABSA)**: Reviews lack detailed sentiment breakdown
3. **No Personalization**: User interest profiles not implemented
4. **No Admin Dashboard**: KPI cards, charts, analytics missing
5. **No Customer Dashboard**: Recommendations not displayed
6. **API Incomplete**: No dedicated /api routes for frontend integrations
7. **Missing DB Tables**:
    - `embeddings` (product vectors)
    - `aspect_sentiments` (ABSA results)
    - `user_profiles` (multi-interest vectors)
    - `login_activity` (audit trail)
    - `recommendation_logs` (tracking)

---

## 1️⃣ PROJECT AUDIT & FILE REVIEW

### ✅ VALIDATION RESULTS

#### Laravel Best Practices

| Aspect                  | Status     | Details                                                       |
| ----------------------- | ---------- | ------------------------------------------------------------- |
| **SOLID Principles**    | ✅ Good    | Service layer properly separates concerns                     |
| **Model Relationships** | ✅ Good    | All relationships correctly defined with proper foreign keys  |
| **Naming Conventions**  | ✅ Good    | Table names plural, columns snake_case, consistent naming     |
| **Mass Assignment**     | ✅ Good    | All models use `$fillable` array (no `$guarded = []`)         |
| **Query Performance**   | ✅ Good    | Eager loading with `->with()`, proper indexes on foreign keys |
| **Authentication**      | ✅ Good    | Fortify integration, session-based auth, 2FA enabled          |
| **Authorization**       | ⚠️ Minimal | No Spatie permissions (only basic is_admin flag)              |
| **Validation**          | ✅ Good    | FormRequests used for all input validation                    |
| **API Structure**       | ❌ Missing | No dedicated API routes (/api prefix)                         |

#### Controllers & Requests

```
✅ Controllers follow RESTful conventions
✅ All controllers have corresponding FormRequests
✅ Business logic moved to Services (not in controllers)
✅ No direct DB queries (all through Eloquent)
⚠️ No API Resource classes (transformers) - using inline arrays
```

#### Services Pattern

```
✅ ProductService: Filtering, pagination, transformations
✅ CartService: Cart operations with transactions
✅ OrderService: Checkout, payment, order creation
✅ ReviewService: Review CRUD with moderation
✅ WishlistService: Wishlist operations
✅ CategoryService: Category hierarchy management
✅ ProductImageService: Image upload/management

🔴 MISSING:
- RecommendationService (core requirement!)
- EmbeddingService (vector operations)
- UserProfileService (interest vectors)
- AnalyticsService (admin dashboard)
```

#### Jobs & Events

```
✅ SendDailySalesReport: Queued job for email reports
✅ SendLowStockEmail: Low stock alerts
✅ ProductStockUpdated: Event for stock changes
✅ SendLowStockNotification: Listener implementation

🔴 MISSING:
- GenerateEmbeddings job
- ProcessABSA job
- UpdateUserProfile job
- LogRecommendation job
```

---

## 2️⃣ DATABASE SCHEMA VALIDATION & IMPROVEMENTS

### Current Schema Analysis

#### Tables Status ✅

```
users                      ✅ Complete (with 2FA)
products                   ✅ Complete (with attributes JSON)
categories                 ✅ Complete (hierarchical)
reviews                    ⚠️ Incomplete (no aspect sentiments)
orders & order_items       ✅ Complete (with payment tracking)
carts & cart_items         ✅ Complete
wishlists                  ✅ Complete
product_images             ✅ Complete (with is_primary flag)
```

#### Schema Issues Found 🔴

| Issue                          | Severity     | Details                      | Fix                |
| ------------------------------ | ------------ | ---------------------------- | ------------------ |
| No `embeddings` table          | **CRITICAL** | Can't store product vectors  | Create migration   |
| No `aspect_sentiments` table   | **CRITICAL** | Can't store ABSA results     | Create migration   |
| No `user_profiles` table       | **HIGH**     | Can't store user interests   | Create migration   |
| No `login_activity` table      | **MEDIUM**   | No audit trail               | Create migration   |
| No `recommendation_logs` table | **MEDIUM**   | Can't track recommendations  | Create migration   |
| Product `views` column missing | **MEDIUM**   | In model but not migration   | Add to migration   |
| No full-text search indexes    | **LOW**      | Slow text search on products | Add fulltext index |

#### Missing Foreign Keys & Indexes

```sql
❌ products.views not in migration (defined in model)
❌ No indexes on:
   - reviews.user_id (for user's reviews)
   - reviews.product_id + rating (for sorting)
   - carts.user_id
   - cart_items.cart_id + product_id

⚠️ Missing composite indexes:
   - orders (user_id, status, created_at) - for user's orders
   - reviews (product_id, is_approved, created_at) - for approved reviews
```

#### JSON Field Usage

```
✅ products.attributes (color, size, etc.)
❌ reviews missing JSON field for aspect_sentiments
✅ orders.shipping_address (could be address JSON)
```

### Recommended Schema Improvements

#### 1. Add Missing `views` Column to Products

```php
// Migration: add_views_to_products_table
Schema::table('products', function (Blueprint $table) {
    $table->unsignedBigInteger('views')->default(0)->after('attributes');
});
```

#### 2. Add Missing Indexes

```php
// Migration: add_missing_indexes_to_products_table
Schema::table('products', function (Blueprint $table) {
    $table->fullText(['name', 'description']); // For full-text search
});

// In reviews migration or new migration
Schema::table('reviews', function (Blueprint $table) {
    $table->index(['user_id', 'created_at']);
    $table->index(['product_id', 'is_approved', 'created_at']);
});

// In orders migration
Schema::table('orders', function (Blueprint $table) {
    $table->index(['user_id', 'status', 'created_at']);
});

// In carts migration
Schema::table('carts', function (Blueprint $table) {
    $table->index('user_id');
});

// In cart_items migration
Schema::table('cart_items', function (Blueprint $table) {
    $table->index(['cart_id', 'product_id']);
});
```

---

## 3️⃣ CRITICAL MISSING TABLES & MIGRATIONS

### A. Embeddings Table (Product Vectors)

```sql
CREATE TABLE embeddings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT UNIQUE NOT NULL,
    embedding LONGBLOB NOT NULL, -- Store as JSON or binary
    model_version VARCHAR(50), -- e.g., "sentence-transformers/all-MiniLM-L6-v2"
    dimension INT, -- 384 for all-MiniLM
    qdrant_id BIGINT, -- Reference to Qdrant vector ID
    metadata JSON, -- Additional data (tags, processing info)
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX (model_version, created_at)
);
```

### B. Aspect Sentiments Table (ABSA Results)

```sql
CREATE TABLE aspect_sentiments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    review_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    aspect VARCHAR(100), -- e.g., "battery", "camera", "design"
    sentiment VARCHAR(20), -- "positive", "negative", "neutral"
    confidence FLOAT, -- 0.0-1.0
    mention_text VARCHAR(255), -- The text mentioning the aspect
    is_emphasized BOOLEAN DEFAULT FALSE, -- Multiple mentions?
    created_at TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX (product_id, aspect),
    INDEX (review_id)
);
```

### C. User Profiles Table (Multi-Interest Vectors)

```sql
CREATE TABLE user_profiles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNIQUE NOT NULL,
    interests_vector LONGBLOB, -- Embedding of user interests
    preferred_categories JSON, -- ["electronics", "fashion"]
    last_interest_update TIMESTAMP,
    model_version VARCHAR(50),
    preference_score FLOAT DEFAULT 0.5, -- 0-1 preference strength
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### D. Login Activity Table (Audit Trail)

```sql
CREATE TABLE login_activity (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    login_at TIMESTAMP,
    logout_at TIMESTAMP NULL,
    status ENUM('success', 'failed', 'suspicious') DEFAULT 'success',
    failure_reason VARCHAR(255),
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (user_id, login_at),
    INDEX (ip_address, login_at)
);
```

### E. Recommendation Logs Table

```sql
CREATE TABLE recommendation_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT,
    product_id BIGINT NOT NULL,
    recommended_product_id BIGINT NOT NULL,
    recommendation_type ENUM(
        'similarity',
        'collaborative',
        'sentiment_based',
        'category_based'
    ),
    score FLOAT, -- Similarity or recommendation score
    rank INT, -- Position in recommendation list
    was_clicked BOOLEAN DEFAULT FALSE,
    was_purchased BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (recommended_product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX (user_id, created_at),
    INDEX (product_id),
    INDEX (recommendation_type)
);
```

---

## 4️⃣ UI COMPONENT RECOMMENDATIONS

### A. Product Card Component

#### Current State: ⚠️ Basic

Located: `resources/js/components/ProductCard.vue`

- Shows image, name, price
- Missing: ratings, sentiment badges, recommendation tags

#### Enhanced Product Card Specification

```vue
<!-- ProductCard.vue -->
<template>
    <div
        class="product-card rounded-lg bg-white shadow transition hover:shadow-lg"
    >
        <!-- Image Section -->
        <div class="relative overflow-hidden rounded-t-lg bg-gray-100">
            <img
                :src="product.image"
                :alt="product.name"
                loading="lazy"
                class="h-48 w-full object-cover transition hover:scale-105"
            />
            <!-- Stock Badge -->
            <div
                v-if="!product.is_in_stock"
                class="absolute top-2 right-2 rounded bg-red-500 px-3 py-1 text-sm text-white"
            >
                Out of Stock
            </div>
            <!-- Recommendation Tag -->
            <div
                v-if="product.is_recommended"
                class="absolute top-2 left-2 flex items-center gap-1 rounded bg-green-500 px-3 py-1 text-sm text-white"
            >
                <StarIcon class="h-4 w-4" /> Recommended
            </div>
            <!-- Discount Badge -->
            <div
                v-if="product.discount_percentage"
                class="absolute right-2 bottom-2 rounded bg-orange-500 px-2 py-1 text-sm font-bold text-white"
            >
                -{{ product.discount_percentage }}%
            </div>
        </div>

        <!-- Content Section -->
        <div class="flex flex-col gap-3 p-4">
            <!-- Category & Brand -->
            <div
                class="flex items-center justify-between text-xs text-gray-500"
            >
                <span class="font-semibold">{{ product.category }}</span>
                <span v-if="product.brand">{{ product.brand }}</span>
            </div>

            <!-- Product Name -->
            <h3 class="line-clamp-2 text-sm font-bold hover:text-blue-600">
                {{ product.name }}
            </h3>

            <!-- Rating -->
            <div class="flex items-center gap-2">
                <RatingStars
                    :rating="product.avg_rating"
                    :count="5"
                    size="sm"
                />
                <span class="text-xs text-gray-500"
                    >({{ product.review_count }} reviews)</span
                >
            </div>

            <!-- Top 2 Aspect Sentiments -->
            <div
                v-if="product.top_sentiments?.length"
                class="flex flex-wrap gap-2"
            >
                <AspectBadge
                    v-for="aspect in product.top_sentiments.slice(0, 2)"
                    :key="aspect.id"
                    :aspect="aspect.name"
                    :sentiment="aspect.sentiment"
                />
            </div>

            <!-- Price Section -->
            <div class="flex items-center gap-2">
                <span class="text-lg font-bold"
                    >Rs {{ product.effective_price }}</span
                >
                <span
                    v-if="product.discount_percentage"
                    class="text-sm text-gray-400 line-through"
                >
                    Rs {{ product.original_price }}
                </span>
            </div>

            <!-- Buttons -->
            <div class="mt-2 flex gap-2">
                <router-link
                    :to="`/products/${product.slug}`"
                    class="flex-1 rounded bg-blue-600 py-2 text-center text-sm font-semibold text-white hover:bg-blue-700"
                >
                    View Details
                </router-link>
                <button
                    @click="toggleWishlist"
                    :class="
                        product.is_wishlisted
                            ? 'bg-red-100 text-red-600'
                            : 'bg-gray-100 text-gray-600'
                    "
                    class="hover:bg-opacity-80 rounded px-3 py-2 transition"
                >
                    <HeartIcon class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { StarIcon, HeartIcon } from 'lucide-vue-next';
import RatingStars from './RatingStars.vue';
import AspectBadge from './AspectBadge.vue';

const props = defineProps<{
    product: {
        id: number;
        name: string;
        slug: string;
        category: string;
        brand?: string;
        image: string;
        original_price: number;
        effective_price: number;
        discount_percentage?: number;
        avg_rating: number;
        review_count: number;
        is_in_stock: boolean;
        is_recommended?: boolean;
        is_wishlisted: boolean;
        top_sentiments?: Array<{
            id: number;
            name: string;
            sentiment: 'positive' | 'negative' | 'neutral';
        }>;
    };
}>();

const emit = defineEmits<{
    wishlist: [id: number, isAdded: boolean];
}>();

const isWishlistLoading = ref(false);

async function toggleWishlist() {
    isWishlistLoading.value = true;
    try {
        await fetch('/wishlist/toggle', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute('content'),
            },
            body: JSON.stringify({ product_id: props.product.id }),
        });
        emit('wishlist', props.product.id, !props.product.is_wishlisted);
    } finally {
        isWishlistLoading.value = false;
    }
}
</script>
```

### B. Review Card Component

```vue
<!-- ReviewCard.vue -->
<template>
    <div class="rounded-lg border p-4 transition hover:shadow-md">
        <!-- Header -->
        <div class="mb-3 flex items-start justify-between">
            <div class="flex items-center gap-3">
                <img :src="review.user_avatar" class="h-10 w-10 rounded-full" />
                <div>
                    <p class="font-semibold">{{ review.user_name }}</p>
                    <p class="text-xs text-gray-500">
                        {{ review.created_ago }}
                    </p>
                </div>
            </div>
            <span
                v-if="review.verified_purchase"
                class="rounded bg-green-100 px-2 py-1 text-xs text-green-700"
            >
                ✓ Verified Purchase
            </span>
        </div>

        <!-- Rating -->
        <RatingStars :rating="review.rating" :count="5" />

        <!-- Review Text -->
        <p class="mt-3 mb-3 text-sm text-gray-700">{{ review.text }}</p>

        <!-- Aspect Sentiment Chips -->
        <div v-if="review.aspects?.length" class="mb-3 flex flex-wrap gap-2">
            <AspectChip
                v-for="aspect in review.aspects"
                :key="aspect.id"
                :aspect="aspect.name"
                :sentiment="aspect.sentiment"
            />
        </div>

        <!-- Moderation Info (Admin only) -->
        <div
            v-if="canModerate"
            class="mb-3 rounded border border-yellow-200 bg-yellow-50 p-2"
        >
            <p class="text-xs font-semibold">
                Moderation Status:
                {{ review.is_approved ? '✓ Approved' : '⊘ Pending' }}
            </p>
            <p v-if="review.moderation_score" class="text-xs text-gray-600">
                Toxicity Score: {{ review.moderation_score }}
            </p>
        </div>

        <!-- Helpfulness Voting -->
        <div
            class="flex items-center gap-4 border-t pt-3 text-sm text-gray-600"
        >
            <button
                @click="voteHelpful"
                class="flex items-center gap-1 hover:text-blue-600"
            >
                <ThumbsUpIcon class="h-4 w-4" /> {{ review.helpful_count }}
            </button>
            <button
                @click="voteUnhelpful"
                class="flex items-center gap-1 hover:text-red-600"
            >
                <ThumbsDownIcon class="h-4 w-4" /> {{ review.unhelpful_count }}
            </button>
            <button
                v-if="canDelete"
                @click="deleteReview"
                class="ml-auto text-red-600 hover:text-red-700"
            >
                Delete
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { ThumbsUpIcon, ThumbsDownIcon } from 'lucide-vue-next'
import RatingStars from './RatingStars.vue'
import AspectChip from './AspectChip.vue'

defineProps<{
  review: {
    id: number
    user_name: string
    user_avatar: string
    rating: number
    text: string
    created_ago: string
    verified_purchase: boolean
    helpful_count: number
    unhelpful_count: number
    is_approved: boolean
    moderation_score?: number
    aspects?: Array<{
      id: number
      name: string
      sentiment: 'positive' | 'negative' | 'neutral'
    }>
  }
  canModerate?: boolean
  canDelete?: boolean
}>()

const emit = defineEmits<{
  delete: [id: number]
  vote: [id: number, type: 'helpful' | 'unhelpful']
}>()

const voteHelpful = () => emit('vote', props.review.id, 'helpful')
const voteUnhelpful = () => emit('vote', props.review.id, 'unhelpful')
const deleteReview = () => emit('delete', props.review.id')
</script>
```

### C. Recommendation Card Component

```vue
<!-- RecommendationCard.vue -->
<template>
    <div
        class="rounded-lg border border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-4"
    >
        <!-- Recommendation Reason -->
        <p class="mb-2 flex items-center gap-1 text-xs font-bold text-blue-700">
            <SparklesIcon class="h-4 w-4" />
            {{ recommendation.reason }}
        </p>

        <!-- Product Reference -->
        <div v-if="recommendation.related_product" class="mb-3">
            <p class="text-xs text-gray-600">Because you liked:</p>
            <p class="text-sm font-semibold text-gray-800">
                {{ recommendation.related_product }}
            </p>
        </div>

        <!-- Recommended Product Preview -->
        <div class="flex gap-3">
            <img
                :src="recommendation.product_image"
                class="h-20 w-20 rounded object-cover"
            />
            <div class="flex-1">
                <h4 class="line-clamp-2 text-sm font-bold">
                    {{ recommendation.product_name }}
                </h4>
                <p class="mt-1 text-xs text-gray-600">
                    Rs {{ recommendation.product_price }}
                </p>

                <!-- Matching Aspects -->
                <div
                    v-if="recommendation.matching_aspects?.length"
                    class="mt-2 flex flex-wrap gap-1"
                >
                    <AspectBadge
                        v-for="aspect in recommendation.matching_aspects"
                        :key="aspect"
                        :aspect="aspect"
                        size="xs"
                    />
                </div>

                <!-- Similarity Score -->
                <div class="mt-2 flex items-center gap-2">
                    <div class="h-2 w-16 rounded-full bg-gray-200">
                        <div
                            class="h-full rounded-full bg-green-500"
                            :style="{ width: recommendation.score * 100 + '%' }"
                        ></div>
                    </div>
                    <span class="text-xs text-gray-600"
                        >{{ Math.round(recommendation.score * 100) }}%
                        match</span
                    >
                </div>
            </div>
        </div>

        <!-- CTA Button -->
        <router-link
            :to="`/products/${recommendation.product_slug}`"
            class="mt-3 block rounded bg-blue-600 py-2 text-center text-sm font-semibold text-white transition hover:bg-blue-700"
        >
            View Product
        </router-link>
    </div>
</template>

<script setup lang="ts">
import { SparklesIcon } from 'lucide-vue-next';
import AspectBadge from './AspectBadge.vue';

defineProps<{
    recommendation: {
        product_name: string;
        product_slug: string;
        product_image: string;
        product_price: number;
        reason: string; // "Similar product", "Popular in your category", etc.
        related_product?: string;
        score: number; // 0-1
        matching_aspects?: string[];
    };
}>();
</script>
```

### D. Aspect Badge Component (Reusable)

```vue
<!-- AspectBadge.vue -->
<template>
    <span :class="sentimentClass"> {{ aspect }} {{ sentimentEmoji }} </span>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        aspect: string;
        sentiment: 'positive' | 'negative' | 'neutral';
        size?: 'sm' | 'md' | 'lg';
    }>(),
    { size: 'md' },
);

const sentimentEmoji = computed(
    () =>
        ({
            positive: '👍',
            negative: '👎',
            neutral: '😐',
        })[props.sentiment],
);

const sentimentClass = computed(() => {
    const baseClass = `inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-semibold`;
    const sizeClass = {
        sm: 'text-xs px-1.5 py-0.5',
        md: 'text-sm px-2 py-1',
        lg: 'text-base px-3 py-1.5',
    }[props.size];

    const colorClass = {
        positive: 'bg-green-100 text-green-700',
        negative: 'bg-red-100 text-red-700',
        neutral: 'bg-gray-100 text-gray-700',
    }[props.sentiment];

    return `${baseClass} ${sizeClass} ${colorClass}`;
});
</script>
```

---

## 5️⃣ RECOMMENDATION SYSTEM INTEGRATION

### Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    FRONTEND (Vue 3)                         │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  ProductCard | ReviewCard | RecommendationCard      │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────────┬────────────────────────────────────┘
                         │
                    [Axios/Fetch]
                         │
┌────────────────────────▼────────────────────────────────────┐
│              LARAVEL API LAYER (Backend)                   │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ GET /api/products/{id}/similar                       │  │
│  │ GET /api/recommendations                             │  │
│  │ GET /api/products (with sentiment filtering)        │  │
│  └──────────────────────────────────────────────────────┘  │
│                         │                                   │
│  ┌──────────────────────▼──────────────────────────────┐  │
│  │     RecommendationService / EmbeddingService       │  │
│  │  - Query Vector DB (Qdrant/FAISS)                  │  │
│  │  - Merge user profiles                             │  │
│  │  - Apply sentiment filters                         │  │
│  └──────────────────────┬──────────────────────────────┘  │
└────────────────────────┬────────────────────────────────────┘
                         │
        ┌────────────────┴────────────────┐
        │                                 │
┌───────▼────────┐         ┌─────────────▼─────────┐
│   QDRANT VDB   │         │  EMBEDDINGS TABLE     │
│  (Production)  │         │  (Fallback/Cache)    │
│                │         │                       │
│ • Product      │         │ • product_id          │
│   vectors      │         │ • embedding (JSON)    │
│ • User vectors │         │ • model_version       │
│ • Similarity   │         │ • metadata            │
│   search       │         │                       │
└────────────────┘         └───────────────────────┘
```

### A. RecommendationService Implementation

```php
<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Embedding;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Get similar products using vector similarity
     *
     * @param Product $product
     * @param int $limit
     * @param User|null $user (for personalization)
     * @return Collection
     */
    public function getSimilarProducts(
        Product $product,
        int $limit = 5,
        ?User $user = null
    ): Collection {
        $cacheKey = "similar_products:{$product->id}:{$limit}";

        return Cache::remember($cacheKey, minutes: 60 * 24, callback: function () use ($product, $limit, $user) {
            // 1. Get product embedding
            $productEmbedding = $this->getProductEmbedding($product);
            if (!$productEmbedding) {
                return $this->getFallbackSimilarProducts($product, $limit);
            }

            // 2. Query Qdrant for similar vectors
            $similarIds = $this->queryQdrant(
                vector: $productEmbedding,
                topK: $limit + 1,
                excludeId: $product->id
            );

            if (empty($similarIds)) {
                return $this->getFallbackSimilarProducts($product, $limit);
            }

            // 3. Fetch products
            $products = Product::whereIn('id', $similarIds)
                ->where('is_active', true)
                ->with(['category', 'images', 'reviews'])
                ->get()
                ->keyBy('id');

            // 4. Re-order by similarity score
            $recommended = collect($similarIds)
                ->take($limit)
                ->map(fn($id) => $products[$id])
                ->filter();

            // 5. Apply user preferences if available
            if ($user) {
                $recommended = $this->filterByUserPreferences($recommended, $user);
            }

            return $recommended;
        });
    }

    /**
     * Get personalized recommendations for user
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getPersonalizedRecommendations(
        User $user,
        int $limit = 10
    ): Collection {
        $cacheKey = "recommendations:user:{$user->id}";

        return Cache::remember($cacheKey, minutes: 60, callback: function () use ($user, $limit) {
            // 1. Get user profile vector
            $userVector = $this->getUserProfileVector($user);
            if (!$userVector) {
                return $this->getContentBasedRecommendations($user, $limit);
            }

            // 2. Query Qdrant for matching products
            $productIds = $this->queryQdrant(
                vector: $userVector,
                topK: $limit + 10,
                filter: ['is_active' => true]
            );

            // 3. Exclude already owned/reviewed products
            $excludeIds = $user->orders()->pluck('product_id')->toArray();
            $reviews = $user->reviews()->pluck('product_id')->toArray();

            $productIds = array_diff($productIds, $excludeIds, $reviews);

            // 4. Fetch and score products
            $products = Product::whereIn('id', array_slice($productIds, 0, $limit))
                ->with(['category', 'images', 'reviews'])
                ->get();

            // 5. Add recommendation reason
            return $products->map(function ($product) use ($user) {
                $product->recommendation_reason = $this->getRecommendationReason($product, $user);
                $product->recommendation_score = $this->calculateRecommendationScore($product, $user);
                return $product;
            })->sortByDesc('recommendation_score')->take($limit);
        });
    }

    /**
     * Get sentiment-aligned recommendations
     *
     * @param User $user
     * @param string $preferredSentiment
     * @param int $limit
     * @return Collection
     */
    public function getSentimentAlignedRecommendations(
        User $user,
        string $preferredSentiment = 'positive',
        int $limit = 5
    ): Collection {
        // Find products with high positive sentiment
        // that user hasn't seen

        $userReviewedProductIds = $user->reviews()->pluck('product_id');

        return Product::where('is_active', true)
            ->whereNotIn('id', $userReviewedProductIds)
            ->with(['reviews', 'category'])
            ->get()
            ->filter(function (Product $product) use ($preferredSentiment) {
                $positiveSentimentCount = $product->reviews()
                    ->whereHas('aspectSentiments', function ($q) {
                        $q->where('sentiment', 'positive');
                    })
                    ->count();

                $totalReviews = $product->reviews()->count();
                $positiveRatio = $totalReviews > 0 ? $positiveSentimentCount / $totalReviews : 0;

                return $positiveSentimentCount > 0 && $positiveRatio >= 0.7;
            })
            ->sortByDesc(fn($p) => $p->rating())
            ->take($limit);
    }

    /**
     * Query Qdrant vector database
     */
    private function queryQdrant(
        array $vector,
        int $topK,
        ?int $excludeId = null,
        array $filter = []
    ): array {
        // Implementation with Qdrant client
        $client = app('qdrant.client');

        try {
            $response = $client->search(
                collection: config('recommendation.qdrant_collection'),
                vector: $vector,
                limit: $topK,
                filter: $filter
            );

            return collect($response['result'])
                ->map(fn($hit) => $hit['id'])
                ->filter(fn($id) => $id !== $excludeId)
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Qdrant query failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get product embedding from cache or regenerate
     */
    private function getProductEmbedding(Product $product): ?array
    {
        $embedding = Embedding::where('product_id', $product->id)
            ->where('model_version', config('recommendation.model_version'))
            ->first();

        if (!$embedding) {
            // Queue job to generate embedding
            \App\Jobs\GenerateProductEmbedding::dispatch($product);
            return null;
        }

        return $embedding->embedding_vector; // Stored as JSON
    }

    /**
     * Get user profile vector
     */
    private function getUserProfileVector(User $user): ?array
    {
        $profile = $user->profile;

        if (!$profile || !$profile->interests_vector) {
            // Queue job to generate user profile
            \App\Jobs\UpdateUserProfile::dispatch($user);
            return null;
        }

        return $profile->interests_vector;
    }

    /**
     * Fallback: category-based recommendations
     */
    private function getFallbackSimilarProducts(Product $product, int $limit): Collection
    {
        return Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with(['images', 'reviews'])
            ->orderBy('views', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Content-based recommendations (no user data)
     */
    private function getContentBasedRecommendations(User $user, int $limit): Collection
    {
        return Product::where('is_active', true)
            ->with(['category', 'images', 'reviews'])
            ->orderBy('views', 'desc')
            ->orderByRaw('(SELECT AVG(rating) FROM reviews WHERE product_id = products.id) DESC')
            ->take($limit)
            ->get();
    }

    /**
     * Calculate recommendation score
     */
    private function calculateRecommendationScore(Product $product, User $user): float
    {
        $baseScore = $product->rating() / 5.0;

        // Category preference boost
        $userCategoryPreference = $user->profile?->preferred_categories ?? [];
        if (in_array($product->category_id, $userCategoryPreference)) {
            $baseScore *= 1.2;
        }

        // Popularity boost
        $baseScore *= (1 + ($product->views / 10000));

        return min($baseScore, 1.0);
    }

    /**
     * Get human-readable recommendation reason
     */
    private function getRecommendationReason(Product $product, User $user): string
    {
        $userCategories = $user->profile?->preferred_categories ?? [];

        if (in_array($product->category_id, $userCategories)) {
            return "Popular in your favorite category";
        }

        if ($product->rating() >= 4.5) {
            return "Highly rated by customers";
        }

        if ($product->views > 1000) {
            return "Trending in this category";
        }

        return "You might like this";
    }

    /**
     * Filter recommendations by user preferences
     */
    private function filterByUserPreferences(Collection $products, User $user): Collection
    {
        $preferredCategories = $user->profile?->preferred_categories ?? [];

        if (empty($preferredCategories)) {
            return $products;
        }

        // Prioritize preferred categories
        return $products->sortByDesc(function (Product $product) use ($preferredCategories) {
            return in_array($product->category_id, $preferredCategories) ? 1 : 0;
        });
    }
}
```

### B. EmbeddingService Implementation

```php
<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Embedding;
use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    /**
     * Generate embedding for product using sentence-transformers
     */
    public function generateProductEmbedding(Product $product): array
    {
        // Prepare product text
        $text = $this->prepareProductText($product);

        // Call Python embedding service
        $embedding = $this->callEmbeddingService($text);

        if (!$embedding) {
            return [];
        }

        // Store in database
        Embedding::updateOrCreate(
            [
                'product_id' => $product->id,
                'model_version' => config('recommendation.model_version'),
            ],
            [
                'embedding' => $embedding,
                'dimension' => count($embedding),
                'metadata' => [
                    'product_name' => $product->name,
                    'category' => $product->category?->name,
                    'generated_at' => now(),
                ]
            ]
        );

        // Store in Qdrant
        $this->storeInQdrant($product, $embedding);

        return $embedding;
    }

    /**
     * Prepare product text for embedding
     */
    private function prepareProductText(Product $product): string
    {
        $parts = [
            $product->name,
            $product->category?->name,
            $product->description,
            implode(' ', array_values($product->attributes ?? [])),
            // Top positive reviews
            $product->reviews()
                ->where('is_approved', true)
                ->orderBy('rating', 'desc')
                ->take(3)
                ->pluck('review')
                ->implode(' '),
        ];

        return implode(' ', array_filter($parts));
    }

    /**
     * Call Python embedding service
     */
    private function callEmbeddingService(string $text): ?array
    {
        try {
            $response = Http::timeout(30)->post(
                config('recommendation.embedding_service_url') . '/embed',
                ['text' => $text]
            );

            if ($response->successful()) {
                return $response->json('embedding');
            }
        } catch (\Exception $e) {
            \Log::error('Embedding service failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Store embedding in Qdrant
     */
    private function storeInQdrant(Product $product, array $embedding): void
    {
        $client = app('qdrant.client');

        try {
            $client->upsertPoints(
                collection: config('recommendation.qdrant_collection'),
                points: [[
                    'id' => $product->id,
                    'vector' => $embedding,
                    'payload' => [
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'category' => $product->category?->name,
                        'price' => $product->effective_price,
                        'is_active' => $product->is_active,
                    ]
                ]]
            );
        } catch (\Exception $e) {
            \Log::error('Qdrant upsert failed: ' . $e->getMessage());
        }
    }
}
```

### C. API Endpoints Implementation

```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __construct(
        protected RecommendationService $recommendationService
    ) {}

    /**
     * GET /api/products/{id}/similar
     * Get similar products for a given product
     */
    public function getSimilarProducts(Product $product, Request $request)
    {
        $limit = $request->input('limit', 5);
        $user = auth()->user();

        $similar = $this->recommendationService->getSimilarProducts(
            $product,
            $limit,
            $user
        );

        return response()->json([
            'data' => $similar->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->effective_price,
                'image' => $p->images->first()?->url,
                'rating' => $p->rating(),
                'reviews_count' => $p->reviews()->count(),
            ]),
            'reason' => 'Similar to ' . $product->name,
        ]);
    }

    /**
     * GET /api/recommendations
     * Get personalized recommendations for authenticated user
     */
    public function getPersonalizedRecommendations(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $limit = $request->input('limit', 10);
        $filter = $request->input('filter'); // 'sentiment', 'category', etc.

        $recommendations = match ($filter) {
            'positive' => $this->recommendationService->getSentimentAlignedRecommendations(
                $user, 'positive', $limit
            ),
            'category' => $this->recommendationService->getContentBasedRecommendations($user, $limit),
            default => $this->recommendationService->getPersonalizedRecommendations($user, $limit),
        };

        return response()->json([
            'data' => $recommendations->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->effective_price,
                'image' => $p->images->first()?->url,
                'rating' => $p->rating(),
                'reason' => $p->recommendation_reason ?? 'Recommended for you',
                'score' => $p->recommendation_score ?? 0.8,
            ]),
            'total' => count($recommendations),
        ]);
    }

    /**
     * GET /api/products
     * Get products with sentiment filtering
     */
    public function listProducts(Request $request)
    {
        $sentimentFilter = $request->input('sentiment'); // 'positive', 'negative'
        $aspect = $request->input('aspect');

        $query = Product::where('is_active', true)
            ->with(['category', 'images', 'reviews']);

        if ($sentimentFilter && $aspect) {
            $query->whereHas('reviews.aspectSentiments', function ($q) use ($sentimentFilter, $aspect) {
                $q->where('aspect', $aspect)
                  ->where('sentiment', $sentimentFilter);
            });
        }

        return response()->json([
            'data' => $query->paginate(12)->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->effective_price,
                'image' => $p->images->first()?->url,
                'rating' => $p->rating(),
            ]),
        ]);
    }
}
```

---

## 6️⃣ ADMIN DASHBOARD DESIGN

### Admin Dashboard Structure (Blade/Vue Components)

```vue
<!-- resources/js/pages/Admin/Dashboard.vue -->
<template>
    <AdminLayout>
        <Head title="Admin Dashboard" />

        <div class="space-y-6">
            <!-- KPI Cards Row 1 -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <KPICard
                    title="Total Users"
                    :value="metrics.total_users"
                    :trend="metrics.user_trend"
                    icon="Users"
                    color="blue"
                />
                <KPICard
                    title="Active Users (7d)"
                    :value="metrics.active_users_7d"
                    :trend="metrics.active_trend"
                    icon="Activity"
                    color="green"
                />
                <KPICard
                    title="Total Products"
                    :value="metrics.total_products"
                    :trend="metrics.product_trend"
                    icon="Box"
                    color="purple"
                />
                <KPICard
                    title="Total Reviews"
                    :value="metrics.total_reviews"
                    :trend="metrics.review_trend"
                    icon="MessageSquare"
                    color="orange"
                />
            </div>

            <!-- KPI Cards Row 2 -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <KPICard
                    title="Avg Rating"
                    :value="metrics.avg_rating.toFixed(2)"
                    suffix="/ 5"
                    icon="Star"
                    color="yellow"
                />
                <KPICard
                    title="Top Category"
                    :value="metrics.top_category"
                    :sub-value="`${metrics.top_category_count} products`"
                    icon="Tag"
                    color="indigo"
                />
                <KPICard
                    title="Revenue (30d)"
                    :value="`Rs ${metrics.revenue_30d}`"
                    :trend="metrics.revenue_trend"
                    icon="DollarSign"
                    color="green"
                />
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Reviews per Day Chart -->
                <Card title="Reviews Trend (30 days)">
                    <LineChart :data="chartData.reviews_per_day" />
                </Card>

                <!-- Sentiment Distribution -->
                <Card title="Sentiment Distribution">
                    <PieChart :data="chartData.sentiment_distribution" />
                </Card>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Top Aspects -->
                <Card title="Top Mentioned Aspects">
                    <BarChart :data="chartData.top_aspects" />
                </Card>

                <!-- Top Products -->
                <Card title="Top Performing Products">
                    <TopProductsTable :products="topProducts" />
                </Card>
            </div>

            <!-- Recent Reviews with Moderation -->
            <Card title="Recent Reviews (Pending Approval)">
                <RecentReviewsTable
                    :reviews="pendingReviews"
                    @approve="approveReview"
                    @reject="rejectReview"
                />
            </Card>

            <!-- Recent Registrations -->
            <Card title="Recent Registrations">
                <RecentUsersTable :users="recentUsers" />
            </Card>

            <!-- System Health -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <HealthCard
                    title="Queue Status"
                    :status="health.queue_status"
                    :message="health.queue_message"
                />
                <HealthCard
                    title="API Latency"
                    :status="health.api_latency"
                    :message="`${health.api_latency_ms}ms avg`"
                />
                <HealthCard
                    title="ML Service"
                    :status="health.ml_service_status"
                    :message="health.ml_service_message"
                />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import KPICard from '@/components/Admin/KPICard.vue';
import Card from '@/components/Admin/Card.vue';
import LineChart from '@/components/Charts/LineChart.vue';
import PieChart from '@/components/Charts/PieChart.vue';
import BarChart from '@/components/Charts/BarChart.vue';
import HealthCard from '@/components/Admin/HealthCard.vue';
import RecentReviewsTable from '@/components/Admin/RecentReviewsTable.vue';
import TopProductsTable from '@/components/Admin/TopProductsTable.vue';
import RecentUsersTable from '@/components/Admin/RecentUsersTable.vue';

const metrics = ref({
    total_users: 0,
    user_trend: 0,
    active_users_7d: 0,
    active_trend: 0,
    total_products: 0,
    product_trend: 0,
    total_reviews: 0,
    review_trend: 0,
    avg_rating: 0,
    top_category: '',
    top_category_count: 0,
    revenue_30d: 0,
    revenue_trend: 0,
});

const chartData = ref({
    reviews_per_day: [],
    sentiment_distribution: [],
    top_aspects: [],
});

const topProducts = ref([]);
const pendingReviews = ref([]);
const recentUsers = ref([]);

const health = ref({
    queue_status: 'healthy',
    queue_message: 'All jobs processed',
    api_latency: 'healthy',
    api_latency_ms: 45,
    ml_service_status: 'healthy',
    ml_service_message: 'Embedding service running',
});

onMounted(async () => {
    // Fetch dashboard data
    const response = await fetch('/api/admin/dashboard');
    const data = await response.json();

    metrics.value = data.metrics;
    chartData.value = data.charts;
    topProducts.value = data.top_products;
    pendingReviews.value = data.pending_reviews;
    recentUsers.value = data.recent_users;
    health.value = data.health;
});

const approveReview = (reviewId) => {
    // API call to approve review
};

const rejectReview = (reviewId) => {
    // API call to reject review
};
</script>
```

### KPI Card Component

```vue
<!-- components/Admin/KPICard.vue -->
<template>
    <div
        :class="`rounded-lg border-l-4 bg-white p-6 shadow border-${colorMap[color]}`"
    >
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ title }}</p>
                <p class="mt-2 text-3xl font-bold">{{ value }}</p>
                <p v-if="subValue" class="mt-1 text-sm text-gray-500">
                    {{ subValue }}
                </p>

                <!-- Trend -->
                <div
                    v-if="trend !== undefined"
                    :class="trend >= 0 ? 'text-green-600' : 'text-red-600'"
                    class="mt-2 flex items-center gap-1 text-sm"
                >
                    <TrendingUpIcon v-if="trend >= 0" class="h-4 w-4" />
                    <TrendingDownIcon v-else class="h-4 w-4" />
                    {{ Math.abs(trend) }}% vs last period
                </div>
            </div>
            <component
                :is="iconComponent"
                :class="`h-12 w-12 text-${colorMap[color]} opacity-20`"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import {
    Users,
    Activity,
    Box,
    MessageSquare,
    Star,
    Tag,
    DollarSign,
    TrendingUp as TrendingUpIcon,
    TrendingDown as TrendingDownIcon,
} from 'lucide-vue-next';

const props = withDefaults(
    defineProps<{
        title: string;
        value: string | number;
        subValue?: string;
        trend?: number;
        icon: string;
        color: 'blue' | 'green' | 'purple' | 'orange' | 'yellow' | 'indigo';
        suffix?: string;
    }>(),
    { color: 'blue' },
);

const colorMap = {
    blue: 'blue-500',
    green: 'green-500',
    purple: 'purple-500',
    orange: 'orange-500',
    yellow: 'yellow-500',
    indigo: 'indigo-500',
};

const iconComponent = computed(
    () =>
        ({
            Users,
            Activity,
            Box,
            MessageSquare,
            Star,
            Tag,
            DollarSign,
        })[props.icon],
);
</script>
```

---

## 7️⃣ CUSTOMER DASHBOARD DESIGN

```vue
<!-- resources/js/pages/Customer/Dashboard.vue -->
<template>
    <PublicLayout>
        <Head title="My Dashboard" />

        <div class="mx-auto max-w-7xl space-y-8 px-4 py-10">
            <!-- Welcome Section -->
            <div
                class="rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white"
            >
                <h1 class="text-3xl font-bold">
                    Welcome back, {{ user.name }}! 👋
                </h1>
                <p class="mt-2 text-blue-100">
                    Discover products tailored just for you
                </p>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <StatCard
                    label="Items in Wishlist"
                    :value="wishlistCount"
                    icon="Heart"
                />
                <StatCard
                    label="Reviews Written"
                    :value="reviewCount"
                    icon="MessageSquare"
                />
                <StatCard
                    label="Orders Placed"
                    :value="orderCount"
                    icon="ShoppingBag"
                />
                <StatCard
                    label="Total Spent"
                    :value="`Rs ${totalSpent}`"
                    icon="DollarSign"
                />
            </div>

            <!-- Your Top Interests -->
            <Card title="Your Top Interests" icon="Sparkles">
                <div class="flex flex-wrap gap-2">
                    <InterestTag
                        v-for="interest in userInterests"
                        :key="interest"
                        :interest="interest"
                    />
                </div>
            </Card>

            <!-- Recently Viewed Products -->
            <Section title="Recently Viewed">
                <ProductCarousel :products="recentlyViewed" />
            </Section>

            <!-- Recommended for You -->
            <Section
                title="Recommended for You"
                :count="recommendations.length"
            >
                <RecommendationGrid :recommendations="recommendations" />
            </Section>

            <!-- Your Wishlist -->
            <Section title="Your Wishlist" :count="wishlist.length">
                <ProductGrid :products="wishlist" />
            </Section>

            <!-- Your Reviews -->
            <Section title="Your Reviews" :count="userReviews.length">
                <ReviewsList :reviews="userReviews" />
            </Section>

            <!-- Recent Orders -->
            <Section title="Recent Orders" :count="recentOrders.length">
                <OrdersList :orders="recentOrders" />
            </Section>

            <!-- Suggested Categories -->
            <Section title="Explore Categories">
                <CategoryGrid :categories="suggestedCategories" />
            </Section>
        </div>
    </PublicLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Card from '@/components/Card.vue';
import Section from '@/components/Section.vue';
import StatCard from '@/components/Customer/StatCard.vue';
import InterestTag from '@/components/Customer/InterestTag.vue';
import ProductCarousel from '@/components/ProductCarousel.vue';
import ProductGrid from '@/components/ProductGrid.vue';
import RecommendationGrid from '@/components/RecommendationGrid.vue';
import ReviewsList from '@/components/ReviewsList.vue';
import OrdersList from '@/components/OrdersList.vue';
import CategoryGrid from '@/components/CategoryGrid.vue';

const page = usePage();
const user = page.props.auth.user;

const wishlistCount = ref(0);
const reviewCount = ref(0);
const orderCount = ref(0);
const totalSpent = ref(0);

const userInterests = ref([]);
const recentlyViewed = ref([]);
const recommendations = ref([]);
const wishlist = ref([]);
const userReviews = ref([]);
const recentOrders = ref([]);
const suggestedCategories = ref([]);

onMounted(async () => {
    // Fetch dashboard data
    const response = await fetch('/api/customer/dashboard');
    const data = await response.json();

    wishlistCount.value = data.wishlist_count;
    reviewCount.value = data.review_count;
    orderCount.value = data.order_count;
    totalSpent.value = data.total_spent;

    userInterests.value = data.user_interests;
    recentlyViewed.value = data.recently_viewed;
    recommendations.value = data.recommendations;
    wishlist.value = data.wishlist;
    userReviews.value = data.user_reviews;
    recentOrders.value = data.recent_orders;
    suggestedCategories.value = data.suggested_categories;
});
</script>
```

---

## 8️⃣ SECURITY & BEST PRACTICES

### Critical Security Issues ⚠️

| Issue                     | Severity   | Current                                     | Fix                                 |
| ------------------------- | ---------- | ------------------------------------------- | ----------------------------------- |
| No Spatie permissions     | **HIGH**   | Only `is_admin` flag                        | Implement role-based access         |
| API rate limiting missing | **HIGH**   | Unlimited requests                          | Add throttle middleware             |
| No CORS setup             | **MEDIUM** | May cause issues with external integrations | Configure CORS in middleware        |
| Weak password policy      | **MEDIUM** | No complexity requirements                  | Add validation rules                |
| No IP whitelisting        | **LOW**    | Anyone can access admin                     | Optional: implement IP restrictions |

### Recommended Security Enhancements

```php
<?php

// config/recommendation.php - New security config
return [
    // Vector DB
    'qdrant_host' => env('QDRANT_HOST', 'localhost'),
    'qdrant_port' => env('QDRANT_PORT', 6333),
    'qdrant_collection' => 'products',

    // Embedding Service
    'embedding_service_url' => env('EMBEDDING_SERVICE_URL', 'http://localhost:8000'),
    'model_version' => 'sentence-transformers/all-MiniLM-L6-v2',

    // Security
    'api_rate_limit' => env('API_RATE_LIMIT', 60), // per minute
    'admin_ip_whitelist' => explode(',', env('ADMIN_IPS', '')),
    'require_https' => env('APP_ENV') === 'production',
];
```

```php
<?php

// app/Http/Middleware/SecureAdminPanel.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureAdminPanel
{
    public function handle(Request $request, Closure $next)
    {
        $adminIps = config('recommendation.admin_ip_whitelist');

        if (!empty($adminIps) && !in_array($request->ip(), $adminIps)) {
            return response()->json(['message' => 'Unauthorized IP'], 403);
        }

        return $next($request);
    }
}
```

```php
<?php

// routes/api.php - New API routes with security
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:60,1', 'auth:sanctum'])->group(function () {
    Route::get('/products/{id}/similar', 'API\RecommendationController@getSimilarProducts');
    Route::get('/recommendations', 'API\RecommendationController@getPersonalizedRecommendations');
});

Route::middleware(['throttle:120,1'])->group(function () {
    Route::get('/products', 'API\ProductController@index');
});
```

### FormRequest Validation Example

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|between:1,5',
            'review' => 'required|string|min:10|max:1000',
            'verified_purchase' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'review.min' => 'Review must be at least 10 characters',
            'review.max' => 'Review cannot exceed 1000 characters',
            'rating.between' => 'Rating must be between 1 and 5',
        ];
    }
}
```

---

## 9️⃣ BACKEND IMPROVEMENTS

### New Services to Implement

#### GenerateProductEmbedding Job

```php
<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Embedding;
use App\Services\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateProductEmbedding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [60, 120, 180];

    public function __construct(public Product $product) {}

    public function handle(EmbeddingService $embeddingService): void
    {
        try {
            $embeddingService->generateProductEmbedding($this->product);
            \Log::info("Embedding generated for product: {$this->product->id}");
        } catch (\Exception $e) {
            \Log::error("Failed to generate embedding: " . $e->getMessage());
            $this->fail($e);
        }
    }
}
```

#### UpdateUserProfile Job

```php
<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateUserProfile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 60;

    public function __construct(public User $user) {}

    public function handle(): void
    {
        // Get user's review history
        $reviews = $this->user->reviews()
            ->with('product.category')
            ->get();

        if ($reviews->isEmpty()) {
            return;
        }

        // Extract categories and sentiments
        $categoryPreferences = $reviews
            ->groupBy('product.category_id')
            ->map(fn($items) => $items->count())
            ->sortDesc()
            ->take(5)
            ->keys()
            ->toArray();

        // Generate user interest vector from reviews
        $interestVector = $this->generateUserVector($reviews);

        // Update or create profile
        $this->user->profile()->updateOrCreate(
            ['user_id' => $this->user->id],
            [
                'interests_vector' => $interestVector,
                'preferred_categories' => $categoryPreferences,
                'last_interest_update' => now(),
                'model_version' => config('recommendation.model_version'),
            ]
        );
    }

    private function generateUserVector(Collection $reviews): array
    {
        // Simplified: average embeddings of reviewed products
        $embeddings = $reviews
            ->map(fn($review) => $review->product->embedding?->embedding_vector)
            ->filter()
            ->toArray();

        if (empty($embeddings)) {
            return [];
        }

        // Calculate mean vector
        return $this->meanVector($embeddings);
    }

    private function meanVector(array $vectors): array
    {
        if (empty($vectors)) {
            return [];
        }

        $dimension = count($vectors[0]);
        $mean = array_fill(0, $dimension, 0);

        foreach ($vectors as $vector) {
            for ($i = 0; $i < $dimension; $i++) {
                $mean[$i] += $vector[$i];
            }
        }

        for ($i = 0; $i < $dimension; $i++) {
            $mean[$i] /= count($vectors);
        }

        return $mean;
    }
}
```

#### ProcessReviewABSA Job

```php
<?php

namespace App\Jobs;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;

class ProcessReviewABSA implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [60, 120];

    public function __construct(public Review $review) {}

    public function handle(): void
    {
        try {
            // Call Python ABSA service
            $response = Http::timeout(30)->post(
                config('recommendation.absa_service_url') . '/analyze',
                ['text' => $this->review->review]
            );

            if ($response->successful()) {
                $aspects = $response->json('aspects', []);

                // Store aspect sentiments
                foreach ($aspects as $aspect) {
                    $this->review->aspectSentiments()->create([
                        'product_id' => $this->review->product_id,
                        'aspect' => $aspect['name'],
                        'sentiment' => $aspect['sentiment'],
                        'confidence' => $aspect['confidence'],
                        'mention_text' => $aspect['text'] ?? null,
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error("ABSA processing failed: " . $e->getMessage());
            throw $e;
        }
    }
}
```

---

## 🔟 MIGRATION FILES TO CREATE

### Migration 1: Add Missing Tables

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Embeddings table
        Schema::create('embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->longText('embedding'); // JSON
            $table->string('model_version');
            $table->integer('dimension');
            $table->bigInteger('qdrant_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'model_version']);
            $table->index('model_version');
        });

        // Aspect Sentiments table
        Schema::create('aspect_sentiments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('aspect');
            $table->enum('sentiment', ['positive', 'negative', 'neutral']);
            $table->float('confidence');
            $table->string('mention_text')->nullable();
            $table->boolean('is_emphasized')->default(false);
            $table->timestamps();

            $table->index(['product_id', 'aspect']);
            $table->index('review_id');
        });

        // User Profiles table
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->longText('interests_vector')->nullable();
            $table->json('preferred_categories')->nullable();
            $table->timestamp('last_interest_update')->nullable();
            $table->string('model_version')->nullable();
            $table->float('preference_score')->default(0.5);
            $table->timestamps();
        });

        // Login Activity table
        Schema::create('login_activity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->ipAddress();
            $table->text('user_agent')->nullable();
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->enum('status', ['success', 'failed', 'suspicious'])->default('success');
            $table->string('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'login_at']);
            $table->index(['ipAddress', 'login_at']);
        });

        // Recommendation Logs table
        Schema::create('recommendation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recommended_product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('recommendation_type', [
                'similarity',
                'collaborative',
                'sentiment_based',
                'category_based'
            ]);
            $table->float('score');
            $table->integer('rank');
            $table->boolean('was_clicked')->default(false);
            $table->boolean('was_purchased')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('product_id');
            $table->index('recommendation_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_logs');
        Schema::dropIfExists('login_activity');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('aspect_sentiments');
        Schema::dropIfExists('embeddings');
    }
};
```

---

## SUMMARY OF CRITICAL ACTIONS

### 🔴 MUST DO (Critical Path)

1. **Create migration** with new tables (embeddings, aspect_sentiments, user_profiles, etc.)
2. **Add Product Model relationships** for embeddings and aspect sentiments
3. **Create RecommendationService** with Qdrant integration
4. **Create API routes** (/api/products/{id}/similar, /api/recommendations)
5. **Add Queue jobs** (GenerateProductEmbedding, UpdateUserProfile, ProcessReviewABSA)
6. **Create admin dashboard** with KPI cards
7. **Implement product cards** with sentiment badges
8. **Add security** (rate limiting, authorization)

### 🟡 SHOULD DO (Important)

- Spatie permissions for role-based access
- ABSA service integration
- Recommendation logging
- Admin review moderation interface
- Customer dashboard with personalization
- Full-text search indexes on products

### 🟢 COULD DO (Nice to Have)

- ML model fine-tuning
- A/B testing for recommendations
- Advanced analytics
- Export functionality
- Webhook integrations

---

**Report Generated**: May 2026  
**Status**: Ready for Implementation  
**Estimated Timeline**: 3-4 weeks for full integration
