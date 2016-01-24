#full config
```
#Tanna User Bundle
tanna_user:
    #only orm for now (mongodb will be added soon)
    db_driver: orm
    #user configuration
    user:
        #required
        class: AppBundle\Entity\User
        #not required
        registration:
            form:
                name: "my_form_name"
                type: "MyBundle\Form\Type\RegistrationType"
                validation_groups: ["Registration", "other"]
        profile:
            form:
                name: "my_form_name"
                type: "MyBundle\Form\Type\ProfileType"
                validation_groups: ["Profile", "other"]
        admin:
            form:
                name: "my_form_name"
                type: "MyBundle\Form\Type\AdminType"
                validation_groups: ["Admin", "other"]
    #group configuration
    group:
        #required
        class: "myUserClass"
```