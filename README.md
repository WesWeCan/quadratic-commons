# Quadratic Commons

[![License](https://img.shields.io/badge/license-CC%20BY%204.0-blue.svg)](https://creativecommons.org/licenses/by/4.0/)

## Description
Quadratic Voting is a collective decision-making procedure that allows you to express the intensity of your preferences on a number of issues. Each voter receives voting credits to spread across several ballot issues. The more a voter spreads their votes, the more voting power is exercised, thanks to the quadratic mathematics behind the procedure. This encourages all voters to compromise and ensures that the group's preferences are accurately represented.

## About Charging the Commons
[Charging the Commons](https://circulateproject.nl/) is a 2-year project that investigates the design of digital platforms for resource communities. The project explores how Situated Design can articulate the values of resource communities- and examines how these values can be translated into designs for the urban commons.

This quadratic voting prototype was designed for the workshop Design for Collective Decision-Making, in cooperation with the [Community Land Trust H-buurt](https://www.clthbuurt.nl/) (Bijlmer, Amsterdam).

Charging the Commons is a cooperation between Amsterdam University of Applied Sciences ([Civic Interaction Design](https://civicinteractiondesign.com/)) and Avans University of Applied Sciences ([Situated Art & Design](https://caradt.nl/research-group/situated-art-and-design/)).

### [Take a look at the demo](https://quadratic-commons.org)


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


## Credits & References
[Design for collective decision making, Wed 31 2024](https://dezwijger.nl/programma/designing-for-resource-sharing-communities)

[The interactive Economist article A Square Vote inspired elements of our interface, especially the flying credit.](https://www.economist.com/interactive/2021/12/18/quadratic-voting)

[RadicalXchange, Glen Weyl & Jack Henderson](https://www.radicalxchange.org/)



## License

This project is licensed under the [CC BY 4.0 License](https://creativecommons.org/licenses/by/4.0/).
