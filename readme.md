Discern Parameters is a light weight php library that helps you rapidly design complex, reusable, strictly typed parameter structures for your functions and class methods.

Discern Parameters eliminates the need for internal type validation and conversion, by providing a fluent, multi-dimensional, and highly configurable interface for defining type requirements in your application.

## Examples

Let's say we're developing a wrapper for an api client that depends on a variety of external parameters. Your method might look something like this:

```
class ApiClient {
  // ...
  public function getComments($user_id, $post_id, array $options, array $headers = [])
  {
    if (!isset($this->base_url)) {
      // throw exception if base url is not initialized
    }

    if (!isset($this->http_client)) {
      // throw exception if client is not initialized
    }

    if (!isset($this->auth_token)) {
      // throw unauthorized exception if auth token isn't provided
    }

    if (!$this->isUuid($user_id)) {
      throw new InvalidArgumentException('`user_id` parameter string is not a valid uuid');
    }

    if (!$this->isUuid($post_id)) {
      throw new InvalidArgumentException('`post_id` parameter string is not a valid uuid');
    }

    if (isset($options['page']) && !is_int($options['page']) || $options['page'] <= 0) {
      throw new InvalidArgumentException('options[`page`] is not valid page number');
    }

    if (isset($options['before_date'])) {
      if (!$options['before_date'] instanceof \DataTime) {
      throw new InvalidArgumentException('options[`before_date`] should be a DateTime object');
      }
    }

    if (isset($options['query'])) {
      //... urlencode the query string if it exists
    }

    //... handle other options

    // build to path
    $path = $this->base_url."/post/{$post_id}/comments";

    // build query options array
    $valid_options = [...] // possible query options
    $query_string = http_build_query(array_intersect_keys($valid_options, $options));

    // build headers 
    $headers = [...]

    return $this->http_client->get($path.'?'.$query_string, $headers);
  }
}
```

The most noticeable problem with the class is the amount of repetitive validation that would be needed to actually execute it. First, we'd need to verify that the parameters given directly to the method are correct, and then secondly, ensuring the class properties are initialized before attempting to use them. Even worse, you'd need to perform a similar validation for any additional method that similarly makes an http request.

Next problem is you can't use the parameters as they are given. There's need to convert them (i.e. options array into a url encoded query string).

All of the above issues are connected to a limited ability in php to natively define complex and nested parameter structures.

### Creating Your Parameter Definitions

Using discern we may define our parameter structure like this:

```
$definition = $template([
  'path' => '{base_url:Url}/post/{post_id:Uuid}/comments',
  'client' => '{http_client:IHttpClient}',
  'options' => [
    'before_date' => '{before_date?:DateTime}',
    'page' => '{page?:Page.int}'
    'query' => '{query?:String.url_encoded}',
    'user_id' => '{user_id:Uuid}'
  ],
  'headers' => [
    'Authorization' => 'Bearer {auth_token:IAuthToken.string}'
  ]
]);
```

With discern parameter definitions, we can ensure we receive the exact parameter types we want (or handle any resulting error), before our method is even called.

Now, our method looks like this instead:

```
function request(SomeCustomClass $params) {
  return $params->http_client(
    $params->path.'?'.$params->options,
    $params->headers
  );
}
```

Notice, we haven't manually validated `options`. Nor did we need to convert `options` manually into a query string (More on that later). We've only described the specific parameters our function needed and received them exactly as expected.

Second thing worth noting, is that the parameter definition will set the properties of the class you determine. In the case above, it's `SomeCustomClass`, giving you even more granular control.

Third, and most importantly, by effectively defining our parameter structure our action method can be less concerned about the nuances of the request (i.e. `getComments`), and more concerned with receiving and sending a valid http request. That means, we can describe the specific details of our request, instead of creating complex functions, which do both validation/conversions and perform actions with the result.

### Initializing Your Object

```
$comments_request = $definition([
  'base_url' => ['https://discernphp.io'],
  'client' => new HttpClient(),
  'post_id' => ['some_uuid'],
  'before_date' => 'yesterday',
  'query' => 'foo bar',
  'user_id' => 'some uuid',
  'auth_token' => ['key', 'secret']
]);

request($comments_request);
```

*Discern parameters uses the values provided as constructor arguments for the defined types, if the value itself is not an instance of the class.* 

By separating the internal properties of the class from the actions that use them, we can avoid repetitive validation or the dreaded `undefined` property error resulting from uninitialized class variables. As a matter of fact, we can totally eliminate the class with it's setter/initialization methods, in favor of using a pure function, as demonstrated above.

### Modifying Your Parameter Structure

You should avoid directly modifying your parameter structure. Instead, use the `with` method.

```
request(
  $comments_request->with([
    'user_id' => ['another user uuid'],
    'auth_token' => ['key', 'secret']
  ])
);
```

The `with` method will clone the parameter structure with newly rendered values. Discern will only re-render the properties containing references to the changed parameters.

***Example***:

```
$new_params = $comments_request->with([
  'user_id' => ['new_uuid']
]);

echo $new_params->path; // 'https://discernphp.io/post/new_uuid/comments'
echo $comments_request->path; // 'https://discernphp.io/post/some_uuid/comments'

var_dump((string) $comments_request->params()->user_id != $new_params->params()->user_id); 
// true

```

### Passing Parameters

Discern parameters can be instances or an array of constructor arguments for those instances.

Meaning:

```
$template([
  'user' => [1,2,3],
]);
```

is the same as:

```
$template([
  'user' => new User(1,2,3),
]);
```

### Managing Types

You have full control of what and how classes are instanced, meaning you’re in control of how type hinting is enforced. You may replace any class definition by implementing the `ParameterFactoryInterface`. See below:

***Example***

```
use Discern\Parameter\Contract\ParameterFactoryInterface;
use Discern\Parameter\Contract\ParameterConfigInterface;
use Discern\Parameter\Param;

class SomeOtherPerson {
  public $id;
  public $first_name;
  public $last_name;
}

class YourTypeFactory implements ParameterFactoryInterface {
  public function invokeParameter(ParameterConfigInterface $config, array $params)
  {
    $person = new SomeOtherPerson();
    $person->id = $params[0];
    $person->first_name = $params[1];
    $person->last_name = $params[2];
    return $person;
  }
}

// add your custom factory
Param::factory()->add(Person::class, new YourTypeFactory());

// create the definition
$template = Param::template();
$definition = $template([
  'house' => [
    'owner' => '{human:Person.first_name} {human.last_name}'
  ]
]);

// pass in the parameters
$object = $definition([
  'owner' => [1, 'Oshane', 'Lee'],
]);

// get the output
echo $object->house['owner']; // outputs `Oshane Lee`.
echo $object->params()->human->id // Outputs `1`
```

In the example above, `YourTypeFactory` is your factory class responsible for creating `Person` instances. It should return a `Person` instance, but has the ability to return any object with a `first_name` property (i.e. the `SomeOtherPerson` class).

###STAR THIS PROJECT

If you think this project is cool, please show it some github love ⭐️!
