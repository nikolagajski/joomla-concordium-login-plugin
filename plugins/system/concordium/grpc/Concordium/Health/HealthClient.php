<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Concordium\Health;

/**
 */
class HealthClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * Check the health of the node. By necessity this involves a number of
     * heuristics since in a distributed network we have to rely on the local
     * information only and we don't have authoritative data on, e.g., last
     * finalized block.
     * 
     * In particular, a node that is not caught up to the head of the chain is not
     * healthy.
     * 
     * If possible the client should use other queries to get a more fine-grained
     * understanding of the node health. However this endpoint should provide a
     * reasonable default and is usable in cases where an automatic check is
     * performed that does not allow for configuration, such as in load-balancers.
     * @param \Concordium\Health\NodeHealthRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Check(\Concordium\Health\NodeHealthRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.health.Health/Check',
        $argument,
        ['\Concordium\Health\NodeHealthResponse', 'decode'],
        $metadata, $options);
    }

}
