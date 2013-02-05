<?php
namespace MongoSession;

class Handler implements \SessionHandlerInterface
{
    protected $collection;
    protected $params;

    public static function initSession($params=array()) {
        $handler = new \MongoSession\Handler($params);
        session_set_save_handler($handler, true);
        session_start();
    }

    public function __construct($params=array())
    {
        $this->params = array_merge(
            [
                'db_name' => 'test',
                'collection_name' => 'session',
                'server' => 'mongodb://localhost:27017',
                'options' => ['connect' => true],
                'write_options' => []
            ],
            $params);
        $this->params['remove_options'] = array_merge(
            $this->params['write_options'], 
            ['justOne' => true]
        );
        $dbName = $this->params['db_name'];
        $collectionName = $this->params['collection_name'];
        $this->collection = (new \MongoClient(
                $this->params['server'], 
                $this->params['options']
            ))
            ->$dbName
            ->$collectionName;
    }

    public function open($savePath, $sessionName)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        return (string)$this->collection->findOne(
            ['_id' => $id], 
            ['d' => true]
        )['d'];
    }

    private function getResult($result) {
        return is_bool($result) ? $result : 1.0 === $result['ok'];
    }

    public function write($id, $data)
    {
        return $this->getResult($this->collection->save(
            ['_id' => $id, 'd' => $data, 't' => new \MongoDate()], 
            $this->params['write_options']
        ));
    }

    public function destroy($id)
    {
        return $this->getResult($this->collection->remove(
            ['_id' => $id], 
            $this->params['remove_options']
        ));
    }

    public function gc($maxlifetime)
    {
        return $this->getResult($this->collection->remove(
            ['t' => ['$lte' => new \MongoDate(time() - $maxlifetime)]], 
            $this->params['write_options']
        ));
    }
}
