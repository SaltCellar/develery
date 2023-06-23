### Steps (Without migration)

    symfony console doctrine:database:create
    symfony console doctrine:schema:update --force

### Steps

Entity [Contact] (Kézzel)    

    symfony console doctrine:database:create
    symfony console make:migration
    symfony console doctrine:migrations:migrate
    symfony console make:form ContactFormType Contact
