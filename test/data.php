<?php

require_once 'vendor/autoload.php';

$simpleDOM = class_exists('\\SimpleDOM');

$t = new \Lum\Test();

$t->plan(18);

class Foo extends \Lum\Data\Arrayish
{
  public function load_simple_xml ($xml, $opts=null)
  {
    $this->tag_name = $xml->getName();
    if (isset($xml->hello))
      $this->hello = (string)$xml->hello;
    if (isset($xml['id']))
      $this->id = (int)(string)$xml['id'];
  }

  public function to_simple_xml ($opts=null)
  {
    $xopts = [];
    if (isset($this->tag_name))
      $xopts['default_tag'] = $this->tag_name;
    $xml = $this->get_simple_xml_element($xopts);
    if (isset($this->hello))
      $xml->addChild('hello', $this->hello);
    if (isset($this->id))
      $xml['id'] = $this->id;
    return $xml;
  }
}

class Bar extends Foo
{
  protected $newconst = true;
}

$json_in = <<<JEND
{
  "id": 1,
  "hello": "World"
}
JEND;

$foo = new Foo($json_in);
$t->is($foo->id, 1, 'JSON input had correct id property');
$t->is($foo->hello, 'World', 'JSON input had correct hello property');

$xml_out = <<<XEND
<?xml version="1.0"?>
<foo id="1"><hello>World</hello></foo>

XEND;

$t->is($foo->to_xml(), $xml_out, 'JSON input returned proper XML output');

$foo = new Foo($xml_out);

$t->is($foo->tag_name, 'foo', 'XML recycled input had correct tag name');
$t->is($foo->id, 1, 'XML recycled input had correct id');

$xml_in = <<<XEND
<?xml version="1.0"?>
<test>
  <hello>Universe</hello>
</test>

XEND;

$foo = new Foo($xml_in);

$t->is($foo->tag_name, 'test', 'XML input had correct tag name');
$t->is($foo->hello, 'Universe', 'XML input had correct hello property');

$t->is($foo->to_xml(['reformat'=>true]), $xml_in, 'XML full circle');

$bar = new Bar(['data'=>$json_in, 'parent'=>$foo]);

$t->is($bar->id, 1, 'Bar had correct id property');
$t->is($bar->parent(), $foo, 'Bar had correct parent');

$stests = 
[
  'SimpleDOM has proper class',
  'SimpleDOM serialized correctly',
];
if ($simpleDOM)
{
  $sdom1 = $bar->to_simple_dom();
  $sdom2 = $foo->to_simple_dom();
  $t->is (get_class($sdom1), 'SimpleDOM', $stests[0]);
  $sdom1->appendChild($sdom2);
  $dom_out = <<<XEND
<?xml version="1.0"?>
<bar id="1">
  <hello>World</hello>
  <test>
    <hello>Universe</hello>
  </test>
</bar>

XEND;
  $t->is ($sdom1->asPrettyXML(), $dom_out, $stests[1]);
}
else
{
  $smsg = 'SimpleDOM not found';
  foreach ($stests as $stest)
  {
    $t->skip($smsg, $stest);
  }
}

// Let's test the to_json() method.
$foo = new Foo(['id'=>1, 'hello'=>'/world']);
$json = $foo->to_json();
$t->is($json, '{"id":1,"hello":"\/world"}', 'to_json() returns proper string');

$json = $foo->to_json(true);
$t->is($json, "{\n    \"id\": 1,\n    \"hello\": \"/world\"\n}", 'to_json(true) returns formatted string');

$foo = new Foo(['nums'=>[1, 2, 3.0, '4', '5.0']]);
$json = $foo->to_json();
$t->is($json, '{"nums":[1,2,3,"4","5.0"]}', 'to_json() numeric handling');
$json = $foo->to_json(['numeric'=>true]);
$t->is($json, '{"nums":[1,2,3,4,5]}', "to_json(['numeric'=>true])");
$json = $foo->to_json(['numbers'=>true]);
$t->is($json, '{"nums":[1,2,3.0,4,5.0]}', "to_json(['numbers'=>true])");
$json = $foo->to_json(['numbers'=>true, 'numeric'=>false]);
$t->is($json, '{"nums":[1,2,3.0,"4","5.0"]}', 'to_json can overwrite group options');

echo $t->tap();
return $t;

