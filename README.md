<h1 align="center"> hydrate </h1>

<p align="center"> 注解处理字段映射问题。</p>


## Installing

```shell
$ composer require z-golly/hydrate -vvv
```

## Usage

```php
use Golly\Hydrate\Entity;

class UserEntity extends Entity
{
    public $name;
    
    public $gender;

    public function toObject($data)
    {
        $entity = parent::toObject($data);
        if($entity->gender == 'm') {
            $entity->gender = 0;
        } else {
            $entity->gender = 1;
        }
          
        return $entity;
    }
}

$user = [
    'name' => 'hello',
    'gender' => 'm'
];
$entity = UserEntity::instance($user);

```

MIT
