# Cerco Search Engine

Implementation of a search engine that includes the ability to search web pages and images related to the input search.

## Stack

- PHP
- MySQL

*Project Structure*

<pre>
.
├── README.md
|
├── <strong>ajax</strong>
│   ├── setBroken.php
|   ├── updateImageCount.php
|   ├── updateLinkCount.php
├── <strong>assets</strong>
│   ├── <strong>*css*</strong>
│   |   └── style.css
|   ├── <strong>*images*</strong>
│   |   |── cerco-logo.png
|   |   |── e.png
|   |   |── end.png
|   |   |── magnifying-glass-search.png
|   |   |── selectedE.png
|   |   └── start.png
|   └── <strong>*javascript*</strong>
│       └── script.js
├── <strong>classes</strong>
│   ├── DomDocumentParser.php
│   ├── ImageResultsProvider.php
│   └── SearchResultsProvider.php
|── <strong>features</strong>
│       └── handleMath.php
|
├── config.php
|── crawl.php
├── index.php
└── search.php
</pre>

The application is run by using XAMPP Control Panel, which is used to start MySQL and run the APACHE server. Once these two modules are running, navigate to

```bash
http://localhost/cerco-search-engine
```

to open the search page and be able to use the application.