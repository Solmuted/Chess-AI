🧠 Chess-AI

Chess-AI is a web application for analyzing chess games using artificial intelligence.
It helps players improve their skills by providing detailed insights into their games.

🚀 Features
📊 Game analysis with AI
📈 Accuracy tracking
❌ Mistake and blunder detection
🧩 Puzzle-like learning from errors
📅 Statistics dashboard (win rate, games played, accuracy trends)
📂 Upload and store your games
🔐 User authentication (login/register)

🛠️ Tech Stack
Backend: Laravel
Frontend: Blade / Bootstrap
Database: MySQL
AI Integration: (например Gemini / Stockfish — укажи что используешь)
⚙️ Installation
git clone https://github.com/your-username/Chess-AI.git
cd Chess-AI

composer install
cp .env.example .env
php artisan key:generate

# настрой БД в .env

php artisan migrate
php artisan serve
🔑 Environment Variables

Добавь в .env:

GEMINI_API_KEY=your_api_key

(или что ты используешь)

📌 Usage
Register or login
Upload your chess game
Analyze it
View stats and improve your play
📈 Future Plans
♟️ Play games directly on the platform
🤖 Stronger AI analysis
📊 Advanced statistics
🌍 Multi-language support
🤝 Contributing

Contributions are welcome!
Feel free to open issues or submit pull requests.

📄 License

This project is open-source and available under the MIT License.

Author

Solmuted