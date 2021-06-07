Bootstrap Menu Bundle
=====================
[![Codeship Status for cameronmurphy/bootstrap-menu-bundle](https://app.codeship.com/projects/bc4e2190-2c19-0137-e09f-1694ad127b99/status?branch=master)](https://app.codeship.com/projects/331254)

A simple [Symfony](https://symfony.com/) bundle for defining your application's menus in configuration and rendering them to work with
[Bootstrap](https://getbootstrap.com/)'s [Navbar](https://getbootstrap.com/docs/5.0/components/navbar/) component. This bundle supports
Bootstrap versions 4 and 5.

Installation
------------
```bash
$ composer require camurphy/bootstrap-menu-bundle
```

Usage
-----
Your menus are defined in `config/packages/bootstrap_menu.yaml`.

Below is a very simple menu called `main` with with only a single 'Logout' link.
```yaml
bootstrap_menu:
  version: 5 # Optional, defaults to Bootstrap 5
  menus:
    main:
      items:
        logout:
          label: 'Logout'
          route: 'app_logout'
```

Then within your template you can render your menu in a Navbar by passing the name of your menu to `render_bootstrap_menu`. This markup is
taken from the [Bootstrap Navbar Fixed example](https://getbootstrap.com/docs/5.0/examples/navbar-fixed/). The Bootstrap 4 version is
[here](https://getbootstrap.com/docs/4.6/examples/navbar-fixed)
```twig
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse"
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav me-auto mb-2 mb-md-0">
        {{ render_bootstrap_menu('main') }}
      </ul>
    </div>
</nav>
```
Result:

![Example 1](https://user-images.githubusercontent.com/1300032/54358791-4f00fb00-46b5-11e9-817c-4b8101305a2b.png)

### Route parameters
Perhaps your route requires parameters. You can also specify these.
```yaml
bootstrap_menu:
  menus:
    main:
      items:
        backorder_report:
          label: 'Backorder Report'
          route: 'app_reports'
          route_parameters:
            id: 'backorder'
```

### External URL
If you would instead like to link to an absolute URL, use `url` instead.
```yaml
bootstrap_menu:
  menus:
    main:
      items:
        disney:
          label: 'Disney'
          url: 'https://www.disney.com'
```

### Dropdowns
Simply by specifying `items` instead of a `route` or `url` to link to, your menu item becomes a Dropdown. Here's an example where a
'Change Password' and a 'Logout' link are nested below an 'Account' dropdown.
```yaml
bootstrap_menu:
  menus:
    main:
      items:
        account:
          label: 'Account'
          items:
            change_password:
              label: 'Change password'
              route: 'app_change_password'
            logout:
              label: 'Logout'
              route: 'app_logout'
```
Result:

![Example 2](https://user-images.githubusercontent.com/1300032/54359374-9fc52380-46b6-11e9-9c0c-bea934d9f0a2.png)

#### Dividers
Dropdowns can also contain [Dividers](https://getbootstrap.com/docs/4.3/components/dropdowns/#dividers) to separate groups of related menu
items.
```yaml
bootstrap_menu:
  menus:
    main:
      items:
        account:
          label: 'Account'
          items:
            change_password:
              label: 'Change password'
              route: 'app_change_password'
            divider:
              is_divider: true
            logout:
              label: 'Logout'
              route: 'app_logout'
```
Result:

![Example 3](https://user-images.githubusercontent.com/1300032/54359921-bf108080-46b7-11e9-8101-faf2526697ef.png)

#### Headers
Dividers that also contain a `label` become [Headers](https://getbootstrap.com/docs/4.3/components/dropdowns/#headers).
```yaml
bootstrap_menu:
  menus:
    main:
      items:
        account:
          label: 'Account'
          items:
            password_divider:
              is_divider: true
              label: 'Password Stuff'
            change_password:
              label: 'Change password'
              route: 'app_change_password'

            other_divider:
              is_divider: true
              label: 'Other Stuff'
            logout:
              label: 'Logout'
              route: 'app_logout'
```
Result:

![Example 4](https://user-images.githubusercontent.com/1300032/54360188-73120b80-46b8-11e9-9af7-6150182b8243.png)

#### Security
Certain parts of the menu may be locked down by role. This following example only allows administrators to change their password.
```yaml
bootstrap_menu:
  menus:
    main:
      items:
        account:
          label: 'Account'
          items:
            password_divider:
              is_divider: true
              label: 'Password Stuff'
            change_password:
              label: 'Change password'
              route: 'app_change_password'
              roles: [ 'ROLE_ADMINISTRATOR' ]

            other_divider:
              is_divider: true
              label: 'Other Stuff'
            logout:
              label: 'Logout'
              route: 'app_logout'
```
For a user without `ROLE_ADMINISTRATOR` they would see:

![Example 5](https://user-images.githubusercontent.com/1300032/54361573-60e59c80-46bb-11e9-89db-669a02f4b82b.png)

The reason for this is Bootstrap Menu Bundle intelligently prunes Dropdowns to remove unnecessary Dividers. Because the user is not
permitted to see any items between 'Password Stuff' and 'Other Stuff', the 'Password Stuff' Divider is also pruned.

Security can also be configured at a Dropdown level. Perhaps only administrators are allowed to use the 'Users' menu.
```yaml
bootstrap_menu:
  menus:
    main:
      items:
        users:
          label: 'Users'
          roles: [ 'ROLE_ADMINISTRATOR' ]
          items:
            user_list:
              label: 'Users'
              route: 'app_user_list'
            new_user:
              label: 'New User'
              route: 'app_new_user'
```
