# Quadratic Commons

[![License](https://img.shields.io/badge/license-CC%20BY%204.0-blue.svg)](https://creativecommons.org/licenses/by/4.0/)

## Description

Quadratic Voting is a collective decision-making process that allows you to express the intensity of your preferences on a number of issues. Instead of a typical 'A/B' vote, you will spread your votes across different issues to exercise more voting power. The more you spread your votes, the more voting power you exercise, thanks to the quadratic mathematics behind the process. This encourages all voters to compromise and ensures that the group's preferences are accurately represented.

## About Charging the Commons
Charging the Commons is a 2-year project that investigates the design of digital platforms for resource communities. The project explores how a Situated-design approach can be employed to articulate the (social) values of resource communities. In addition, the project examines how these values can be translated into a design for the management of an urban commons.

This quadratic voting prototype was created for the workshop Design for collective decision-making is part of the research project Charging the Commons, a cooperation between Amsterdam University of Applied Sciences (Civic Interaction Design) and Avans University of Applied Sciences (Situated Art & Design)

### [Take a look at the demo](https://quadratic-commons.nl)


## Table of Contents

- [Installation](#installation)
- [Technology](#technology)
- [License](#license)

## Technology
This project uses the following technologies:
- Laravel (v10.10)
- PHP (Version 8.1 or higher)
- InertiaJs
- Vue.js 3 (TypeScript)


## Installation


To install the Quadratic Commons project, follow these steps:


1. Clone the repository:
    ```
    git clone https://github.com/WesWeCan/quadratic-commons.git
    ```

2. Navigate to the project directory:
    ```
    cd quadratic-commons
    ```

3. Create a `.env` file by copying the example file:
    ```
    cp .env.example .env
    ```

4. Set up your database configuration in the `.env` file.

5. Generate a new application key:
    ```
    php artisan key:generate
    ```

6. Run the database migration:
    ```
    php artisan migrate
    ```

7. Start the development server:
    ```
    php artisan serve
    ```


Now you can access the Quadratic Commons project in your browser at `http://localhost:8000`.


Note: Make sure you have Laravel (v10.10) and PHP (Version 8.1 or higher) installed on your system before proceeding with the installation.

### Using Yarn

If you prefer to use Yarn as your package manager, you can follow these additional steps:

1. Install Yarn globally:
    ```
    npm install -g yarn
    ```
2. Install project dependencies using Yarn:
    ```
    yarn install
    ```
3. Run the dev server
    ```
    yarn dev
    ```


## License

This project is licensed under the [CC BY 4.0 License](https://creativecommons.org/licenses/by/4.0/).