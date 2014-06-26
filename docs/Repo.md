# Repo

Configuration Method                   | Description
---------------------------------------|-------------
__setModelClass__($class)              | Set the corresponding model class, required
__setTable__($table)                   | Set the name of the database table, defaults to the short class name of the repo
__setDb__($dbName)                     | Set alternative database connection
__setSoftDelete__($isSoftDelete)       | Set to true if you want this model to be soft deleted. More on [soft delete later](/docs/SoftDelete.md)
__setInherited__($isInherited)         | Set to true if this repo will be inherited by other repo using [Single table inheritance](/docs/Inherited.md)
__setRootRepo__(AbstractRepo $repo)    | Used for children in single table inheritence
__setPrimaryKey__($primaryKey)         | Sets the property/column to be used for primary key, "id" by default
__setNameKey__($nameKey)               | Sets the property/column to be used for name key - will be used for findByName method on the repo. Defaults to "name"
__addRel__(AbstractRel $rel)           | Add a link to a related model. Read about [Relations](/docs/Relations.md)
__addRels__(array $rels)               | Add multiple rels .
__addAssert__(AbstractAssert $assert)  | Add an assertion for this model. Read about [Assertions](/docs/Assertions.md)
__addAsserts__(array $asserts)         | Add multiple asserts
__addSerializer__(AbstractSerializer)  | Add a property serializer. Read about [Serializers](/docs/Serializers.md)
__addSerializers__(array $serializers) | Add multiple serializers
__addEventBefore__($event, $callback)  | Add event listener, to be triggered before a specific event
__addEventAfter__($event, $callback)   | Add event listener to be triggered after a specific event
