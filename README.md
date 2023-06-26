# Lunr.Gravity

Lunr.Gravity is a database library aiming to abstract SQL features rather than SQL databases. It guarantees that if you
use a specific SQL feature (e.g. JOIN), it will work identical for all databases that support that feature. It, however,
will not guarantee that every database supports that feature. In this way the **developer** is responsible for selecting
the features that would allow switching out one database with another, if that is a desired workflow. But on the flipside
this also allows developers who know they'll depend on a specific database to make the most use out of it.

Installation
------------

* Install Lunr.Gravity with Composer or with your own installer.
* Lunr.Gravity follows the [semantic versioning][2] standards.

Community
---------

* [Join the Lunr usergroup][3] or dive right into development with the [developer group][4].
* Follow us on [GitHub][5].

Contributing
------------

Lunr.Gravity is an Open Source, community-driven project. Join by contributing code or documentation.
To do that, make your changes and upload them to our reviewboard at [reviews.lunr.nl][6]

If you encounter any issues when using Lunr.Gravity you can report them [on github][7]

About Us
--------

Lunr.Gravity development is spearheaded by [Move][1].

  [1]: https://moveagency.com
  [2]: https://semver.org
  [3]: https://groups.google.com/forum/#!forum/lunr-users
  [4]: https://groups.google.com/forum/#!forum/lunr-developers
  [5]: https://github.com/lunr-php/lunr.gravity
  [6]: https://reviews.lunr.nl
  [7]: https://github.com/lunr-php/lunr.gravity/issues
