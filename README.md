[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]


  <h3 align="center">Basic Laravel Multitenancy</h3>

  <p align="center">
    Small package for building multi-tenant SaaS with Laravel. Multitenancy is transparently supported in Eloquent, allowing for simple and secure partitioning of data without requiring developers to perform explicit tenant validation. 
    <br />
    <a href="https://github.com/sjaslow/laravel-multitenancy/issues">Report Bug</a>
    ·
    <a href="https://github.com/sjaslow/laravel-multitenancy/issues">Request Feature</a>
  </p>
</p>



<!-- TABLE OF CONTENTS -->
<details open="open">
  <summary><h2 style="display: inline-block">Table of Contents</h2></summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgements">Acknowledgements</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

Many SaaS systems require the concept of multi-tenancy; allowing multiple customers to use the same system (and same backing store) while keeping each customer's own data separate and confidential, inaccessible to any other customers.

A simplistic (unused by this package) solution for Multitenancy would be to give each customer their own app instance and/or database. However, this approach does not scale well. As the number of customers grows, the effort in schema updates and app configuration grows as well.

So isntead, this package makes use of a "tenant_id" differentiator column on tables that are directly associated with customers, such as users and top-level domain objects. By adding the "MultiTenantTrait" to your Laravel models, a global scope ensures that Eloquent queries only return data for the currently logged-in customer. 

**Note:** This package was conceived around 2010, before other packages such as stancl/tenancy were developed. Other, more robust packages now exist - suggest checking them out!


### Built With

* Emacs, of course


<!-- USAGE EXAMPLES -->
## Usage

Usage is simple. Add a "tenant_id" column in a migration for one or more of your domain models. Then:

1. Add the Provider manually to app.php, if you're on older Laravel:
   ```
   Ngdcorp\Multitenancy\MultitenancyServiceProvicer::class
   ```
2. Add the trait to your model class(es):
   ```
   use MultiTenantTrait;
   ```
3. All set!

### Disabling / Cross-tenant queries
For security, tenant checks are enabled by default and must be explicitly disabled if you want to write code for cross-tenant queries. To do this, call the "disable" method:
```
MultiTenantScope::disable();
```

Now all Eloquent queries will ignore the tenant_id column and its restrictions. To re-enable multitenancy checks, use the "enable" method:
 ```
MultiTenantScope::enable();
```


<!-- ROADMAP -->
## Roadmap

This package is not updated, newer third-party packages now exist. However, since it is used in several production systems, it is sometimes updated to match changes in Laravel/Eloquent.


<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE` for more information.


<!-- CONTACT -->
## Contact

Seth Jaslow - sjaslow@ngdcorp.com
Project Link: [https://github.com/sjaslow/laravel-multitenancy](https://github.com/sjaslow/laravel-multitenancy)




<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/sjaslow/laravel-multitenancy.svg?style=for-the-badge
[contributors-url]: https://github.com/sjaslow/laravel-multitenancy/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/sjaslow/laravel-multitenancy.svg?style=for-the-badge
[forks-url]: https://github.com/sjaslow/laravel-multitenancy/network/members
[stars-shield]: https://img.shields.io/github/stars/sjaslow/laravel-multitenancy.svg?style=for-the-badge
[stars-url]: https://github.com/sjaslow/laravel-multitenancy/stargazers
[issues-shield]: https://img.shields.io/github/issues/sjaslow/laravel-multitenancy.svg?style=for-the-badge
[issues-url]: https://github.com/sjaslow/laravel-multitenancy/issues
[license-shield]: https://img.shields.io/github/license/sjaslow/laravel-multitenancy.svg?style=for-the-badge
[license-url]: https://github.com/sjaslow/laravel-multitenancy/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://www.linkedin.com/in/sethjaslow
