<a name="readme-top"></a>

<!-- PROJECT LOGO -->
<br />
<div align="center">
  <!-- <a href="https://github.com/AndreasLrx/ECommerce">
    <img src="/app/public/logo.png" alt="Logo" width="350" height="auto">
  </a> -->

<h3 align="center">Trottin'Old</h3>

  <p align="center">
    An awesome e-commerce API to make money!
    <br />
    <br />
    <br />
  </p>
</div>

<!-- ABOUT THE PROJECT -->

## About The Project

This is the sixth Epitech project in the Pre-MSc cursus. This project aims at creating a E-Commerce API with a complete deployment cycle using ansible.

### Back-End

- Use either Symfony 6.\* or Django framework
- Handle user authentication (register/login) with authenticated endpoints
- Allow user to make orders of multiple products using a buffer cart

### Deployment

- Use ansible
- Deploy on a Debian 11

### Bonus

In addition to those constraints, we aim to add some BONUSES:

- Continuous integration
- API endpoints documentation
- Divided permissions for product, order... managements

 <p align="right">(<a href="#readme-top">Back to the top</a>)</p>

---

### Built With

Why ? Because we want to discover new technologies in that project

- [![Symfony][symfony.com]][symfony-url]
- [![Ansible][ansible.com]][ansible-url]

<p align="right">(<a href="#readme-top">Back to the top</a>)</p>

---

<!-- GETTING STARTED -->

## Getting Started

### Prerequisites

You must install:

- ansible
- symfony
- composer
- docker (optional: to run the database)

### Installation

0. Make yourself a good coffee

1. Clone the repo

   ```sh
   git clone https://github.com/AndreasLrx/ECommerce.git
   ```

2. Go in the back-end application directory

   ```sh
   cd ./app
   ```

3. Install the dependencies

   ```sh
   composer install
   ```

4. Migrate the database

   ```sh
   ./bin/console doctrine:migrations:migrate
   ```

5. Configure the database

   In case you don't have a database configured, you can start the database using docker.
   You may want to set some MARIA_DB\* environnement variables before
   (see the [docker-compose.yml](app/docker-compose.yml)).

   ```sh
   docker-compose up
   ```

   Then update the DATABASE_URL variable in the [.env](app/.env) file according to your settings.

6. Start the symfony project

   ```sh
   symfony server:start
   ```

      <p align="right">(<a href="#readme-top">Back to the top</a>)</p>

---

NOW YOU KNOW EVERYTHING ABOUT Trottin'Old (except the origin of the name), FEEL FREE TO EXPLORE THE API AND HAVE FUN WITH IT.

<p align="right">(<a href="#readme-top">Back to the top</a>)</p>

---

<!-- ACKNOWLEDGMENTS -->

## Acknowledgments

Some useful links we used during the project and would like to give credit to.

- [Markdown badges](https://github.com/Ileriayo/markdown-badges)

<p align="right">(<a href="#readme-top">Back to the top</a>)</p>

---

# Thank you for reading this README

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->

[symfony.com]: https://img.shields.io/badge/symfony-%23000000.svg?style=for-the-badge&logo=symfony&logoColor=white
[symfony-url]: https://symfony.com/
[ansible.com]: https://img.shields.io/badge/ansible-%231A1918.svg?style=for-the-badge&logo=ansible&logoColor=white
[ansible-url]: https://ansible.com/
