# ♟️ Chess-AI

**Chess-AI** is a web application for analyzing chess games using artificial intelligence (Groq API).  
It helps players improve their skills by providing detailed insights, detecting mistakes, and offering personalized training.

---

## 🚀 Features

- 📊 Game analysis powered by **Groq AI**
- 📈 Accuracy tracking per game
- ❌ Mistake and blunder detection
- 🧩 Chess puzzles for skill improvement
- 📅 Statistics dashboard (win rate, games played, accuracy trends)
- 🎓 Courses and lessons system
- 🤖 AI Trainer — play and learn with an AI opponent
- 🔐 User authentication (register / login)
- 👤 User profile with rating and level

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 10 |
| Frontend | Blade + Tailwind CSS |
| Database | MySQL |
| AI | Groq API |
| Build | Vite |

---

## ⚙️ Installation

```bash
git clone https://github.com/Solmuted/Chess-AI.git
cd Chess-AI

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Настройте базу данных в `.env`:

```env
DB_DATABASE=chess_ai
DB_USERNAME=root
DB_PASSWORD=your_password
```

Добавьте Groq API ключ в `.env`:

```env
GROQ_API_KEY=your_groq_api_key
```

Запустите миграции и сидеры:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

---

## 🔑 Получить Groq API ключ

1. Зайдите на [console.groq.com](https://console.groq.com)
2. Зарегистрируйтесь / войдите
3. Перейдите в **API Keys** → **Create API Key**
4. Скопируйте ключ в `.env`

---

## 📌 Использование

1. Зарегистрируйтесь или войдите в аккаунт
2. Пройдите диагностику для определения уровня
3. Загрузите партию для анализа
4. Изучайте курсы и решайте пазлы
5. Тренируйтесь с AI-тренером
6. Отслеживайте прогресс в дашборде

---

## 📁 Структура проекта

```
app/
├── Http/Controllers/
│   ├── DiagnosticController.php   # AI диагностика уровня
│   ├── TrainerController.php      # AI тренер
│   ├── PuzzleController.php       # Шахматные пазлы
│   ├── CourseController.php       # Курсы и уроки
│   ├── ProgressController.php     # Статистика прогресса
│   └── DashboardController.php    # Главная панель
├── Models/
│   ├── User.php
│   ├── DiagnosticResult.php
│   ├── GameSession.php
│   ├── Puzzle.php
│   ├── Course.php
│   └── ProgressStat.php
database/
├── migrations/                    # Все миграции
└── seeders/                       # Курсы и пазлы
resources/views/                   # Blade шаблоны
```

---

## 🤝 Contributing

Вклад приветствуется! Открывайте issues или создавайте pull requests.

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

---

**Author:** [Solmuted](https://github.com/Solmuted)
