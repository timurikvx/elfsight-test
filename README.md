# Elfsight Php Home Task


Symfony version: 7.3.0

The library `davmixcool/php-sentiment-analyzer` is used as sentiment analysis

## Install
1. Clone the repository:
   ```bash
   git clone https://github.com/timurikvx/elfsight-test.git
   ```
2. Install dependencies:
   ```bash
   composer install
   ```
3. Execute migrations:
   ```bash
   php bin/console doctrine:migration:migrate
   ```

## Install by Docker
1. Clone the repository:
   ```bash
   git clone https://github.com/timurikvx/elfsight-test.git
   ```
2. Run docker compose:
   ```bash
   docker compose up --build -d
   ```
3. Docker will automatically install dependencies, execute migrations and download the episode list.

### **This project is configured to be deployed via docker.**

## Import episodes

There are 2 ways to import episodes:
- Via the command:
```bash
php bin\console episode:import
```
- HTTP:
```http
POST /api/episodes/import
```

## Endpoints

- ### Review episode

**POST** `/api/episode/review`

**Input body:**
```json
{
    "id": 23,
    "text": "It`s great episode"
}

```

**POST** `/api/episode/review/{episode id}`

**Input body:**
```json
{
    "text": "It`s great episode"
}

```

**Output body:**

```json
{
    "rate": 0
}
```

### Get summary episode

**POST** `/api/episode/summary/{episode id}`

No input data

Output body:
```json
{
    "name": "Pilot",
    "date": "2013-12-02",
    "rate": 0.44,
    "last_reviews": [
        "It`s great episode",
        "It`s review",
        "AMAZING!!!"
    ]
}

```

### Get all episodes list

**POST** `/api/episodes/list`

No input data

Output body:
```json
[
    {
        "ID": 1,
        "name": "Pilot",
        "Date": {
            "date": "2013-12-02 00:00:00.000000",
            "timezone_type": 3,
            "timezone": "Europe/Berlin"
        },
        "episode": "S01E01",
        "avg_rate": 0.44
    },
    {
        "ID": 2,
        "name": "Lawnmower Dog",
        "Date": {
            "date": "2013-12-09 00:00:00.000000",
            "timezone_type": 3,
            "timezone": "Europe/Berlin"
        },
        "episode": "S01E02",
        "avg_rate": null
    }
]

```

### A short description of the project

In addition to Symfony, the project also uses standard Redis-based caching.
I thought about whether to use RabbitMQ, but decided that the project did not require it
The description did not say that the project was meant to be highly loaded, so I focused on the architecture.
My principles included maximum code reuse and it`s minimization

I also decided to use validation to check the incoming data.




