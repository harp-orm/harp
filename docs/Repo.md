# Repo

Configuration Method                   | Description
---------------------------------------|-------------
``setModelClass($class)``              | Set the corresponding model class, required
``setTable($table)``                   | Set the name of the database table, defaults to the short class name of the repo
``setDb($dbName)``                     | Set alternative database connection
``setSoftDelete($isSoftDelete)``       | Set to true if you want this model to be soft deleted. More on [soft delete later](/docs/SoftDelete.md)
``setInherited($isInherited)``         | Set to true if this repo will be inherited by other repo using [Single table inheritance](/docs/Inherited.md)
``setRootRepo(AbstractRepo $repo)``    | Used for children in single table inheritence
``setPrimaryKey($primaryKey)``         | Sets the property/column to be used for primary key, "id" by default
``setNameKey($nameKey)``               | Sets the property/column to be used for name key - will be used for findByName method on the repo. Defaults to "name"
``addRel(AbstractRel $rel)``           | Add a link to a related model. Read about [Relations](/docs/Relations.md)
``addRels(array $rels)``               | Add multiple rels .
``addAssert(AbstractAssert $assert)``  | Add an assertion for this model. Read about [Assertions](/docs/Assertions.md)
``addAsserts(array $asserts)``         | Add multiple asserts
``addSerializer(AbstractSerializer)``  | Add a property serializer. Read about [Serializers](/docs/Serializers.md)
``addSerializers(array $serializers)`` | Add multiple serializers
``addEventBefore($event, $callback)``  | Add event listener, to be triggered before a specific event
``addEventAfter($event, $callback)``   | Add event listener to be triggered after a specific event
