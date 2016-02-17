# WH ADMIN BUNDLE
---
Le template utilisé est smart admin. 

## Installation

    public function registerBundles()
    {
        $bundles = array(

            new WH\SmartAdminBundle\WHSmartAdminBundle(),

        );

        ...


## Twig functions

**.dateSelect : Met une taille à 33% des selects pour qu'ils tiennent dans l'espace en inline :**

    {{ form_row(formCreate.birthday, {'class' : 'dateSelect'}) }}


**ou 20% :**

    {{ form_row(formCreate.birthday, {'class' : 'dateTimeSelect'}) }}


**.block Met un element en display:block**


**Demander une confirmation avant suppression :**

    {{ Smart.confirm('your text') }}

**Modal :**

    {{ Smart.modal('size') }}

'size' peut être rien (taille moyenne) ou mm, sm, lg

# SmartAdminBundle
