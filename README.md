# Banking System

This is a banking system project built with Laravel and React. It allows users to perform deposit and withdrawal operations.

## Installation

1. Clone the repository to your local machine.


2. Navigate to the project directory.


3. Install the project dependencies using Composer.


4. Create a copy of the `.env.example` file and rename it to `.env`. Update the necessary configuration values such as database credentials.


5. Generate an application key.


6. Run the database migrations and seed the database with sample data.


7. Install the JavaScript dependencies using npm.


## Usage

1. Start the development server.


3. Access the application by visiting [http://localhost:8000](http://localhost:8000) in your web browser.

4. Register a new user account or login with existing credentials.

5. Perform deposit and withdrawal operations using the provided routes.

## API Routes

- `POST /users`: Create a new user with the provided name and account type.
- `POST /login`: Login user with the email and password.
- `GET /`: Show all the transactions and current balance.
- `GET /deposit`: Show all the deposited transactions.
- `POST /deposit`: Accept the user ID and amount, and update the user's balance by adding the deposited amount.
- `GET /withdrawal`: Show all the withdrawal transactions.
- `POST /withdrawal`: Accept the user ID and amount, and update the user's balance by deducting the withdrawn amount.

Please refer to the source code for more details on the implementation of these routes.

## License

This project is licensed under the [MIT License](LICENSE).
