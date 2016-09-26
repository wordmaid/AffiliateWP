### AffiliateWP Supplemental docs

This file provides a non-empty directory for inclusion into the repository,
as well as instructions on how to add a supplemental doc to AffiliateWP
Developer documentation.

----

- Supplemental docs can be added by creating a new `.md` doc in this directory. 

- The doc must have a filename matching the hook, function, method, or class for which you'd like to provide documentation.

- Supplemental docs are injected as an option for complete replacement of the core-generated documentation. It's recommended to add inline documentation over supplemental docs whenever possible. 

- Supplemental docs injection is a good fit when:
    -  The inline docs are so large that adding it to the source file would impact the file size or appearance more than desired.
    - You'd like to add at-length customization or code exmaples beyond what's practical within the source itself, such as code snippets
    - Notes, links, or suggestions not _directly_ relating to the property.
    


----


<sup>Last revision: `Mon Sep 26 12:27:08 EDT 2016`</sup>
