<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Concordium;

/**
 */
class P2PClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * Suggest to a peer to connect to the submitted peer details.
     * This, if successful, adds the peer to the list of given addresses.
     * @param \Concordium\PeerConnectRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function PeerConnect(\Concordium\PeerConnectRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/PeerConnect',
        $argument,
        ['\Concordium\BoolResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Disconnect from the peer and remove them from the given addresses list
     * if they are on it. Return if the request was processed successfully.
     * @param \Concordium\PeerConnectRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function PeerDisconnect(\Concordium\PeerConnectRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/PeerDisconnect',
        $argument,
        ['\Concordium\BoolResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Uptime of the *node* in milliseconds.
     * @param \Concordium\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function PeerUptime(\Concordium\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/PeerUptime',
        $argument,
        ['\Concordium\NumberResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Total number of sent packets by the node.
     * @param \Concordium\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function PeerTotalSent(\Concordium\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/PeerTotalSent',
        $argument,
        ['\Concordium\NumberResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Total number of received packets by the node.
     * @param \Concordium\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function PeerTotalReceived(\Concordium\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/PeerTotalReceived',
        $argument,
        ['\Concordium\NumberResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Node software version.
     * @param \Concordium\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function PeerVersion(\Concordium\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/PeerVersion',
        $argument,
        ['\Concordium\StringResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Stats for connected peers.
     * @param \Concordium\PeersRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function PeerStats(\Concordium\PeersRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/PeerStats',
        $argument,
        ['\Concordium\PeerStatsResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * List of connected peers.
     * @param \Concordium\PeersRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function PeerList(\Concordium\PeersRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/PeerList',
        $argument,
        ['\Concordium\PeerListResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Ban a the given peer.
     * @param \Concordium\PeerElement $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function BanNode(\Concordium\PeerElement $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/BanNode',
        $argument,
        ['\Concordium\BoolResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Unban the given peer.
     * @param \Concordium\PeerElement $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function UnbanNode(\Concordium\PeerElement $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/UnbanNode',
        $argument,
        ['\Concordium\BoolResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Join the provided network.
     * @param \Concordium\NetworkChangeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function JoinNetwork(\Concordium\NetworkChangeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/JoinNetwork',
        $argument,
        ['\Concordium\BoolResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Leave the provided network.
     * @param \Concordium\NetworkChangeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function LeaveNetwork(\Concordium\NetworkChangeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/LeaveNetwork',
        $argument,
        ['\Concordium\BoolResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get information about the running node.
     * @param \Concordium\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function NodeInfo(\Concordium\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/NodeInfo',
        $argument,
        ['\Concordium\NodeInfoResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get information about the consensus.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetConsensusStatus(\Concordium\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetConsensusStatus',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get information about the block.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\BlockHash $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetBlockInfo(\Concordium\BlockHash $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetBlockInfo',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get ancestors for the provided block.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\BlockHashAndAmount $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetAncestors(\Concordium\BlockHashAndAmount $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetAncestors',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get the current branches.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetBranches(\Concordium\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetBranches',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get the blocks at the given height.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\BlockHeight $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetBlocksAtHeight(\Concordium\BlockHeight $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetBlocksAtHeight',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Submit a transaction.
     * @param \Concordium\SendTransactionRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function SendTransaction(\Concordium\SendTransactionRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/SendTransaction',
        $argument,
        ['\Concordium\BoolResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Start the baker in the consensus module.
     * @param \Concordium\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function StartBaker(\Concordium\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/StartBaker',
        $argument,
        ['\Concordium\BoolResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Stop the baker in the consensus module.
     * @param \Concordium\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function StopBaker(\Concordium\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/StopBaker',
        $argument,
        ['\Concordium\BoolResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get a list of accounts that exist in the state after the given block.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\BlockHash $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetAccountList(\Concordium\BlockHash $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetAccountList',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get all smart contract instances that exist in the state after the given block.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\BlockHash $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetInstances(\Concordium\BlockHash $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetInstances',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get information about an account.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\GetAddressInfoRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetAccountInfo(\Concordium\GetAddressInfoRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetAccountInfo',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get information about a smart contract instance.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\GetAddressInfoRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetInstanceInfo(\Concordium\GetAddressInfoRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetInstanceInfo',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Invoke a smart contract instance and view the result *as if* it had been updated at the end of the provided block.
     * This is *not* a transaction.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\InvokeContractRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function InvokeContract(\Concordium\InvokeContractRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/InvokeContract',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get an overview of the balance of special accounts in the given block.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\BlockHash $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetRewardStatus(\Concordium\BlockHash $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetRewardStatus',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get an overview of the parameters used for baking.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\BlockHash $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetBirkParameters(\Concordium\BlockHash $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetBirkParameters',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get a list of smart contract modules that exist in the state after the given block.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\BlockHash $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetModuleList(\Concordium\BlockHash $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetModuleList',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get the source of a smart contract module.
     * @param \Concordium\GetModuleSourceRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetModuleSource(\Concordium\GetModuleSourceRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetModuleSource',
        $argument,
        ['\Concordium\BytesResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get a list of all identity providers that exist in the state after the given block.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\BlockHash $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetIdentityProviders(\Concordium\BlockHash $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetIdentityProviders',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get a list of all anonymity revokers that exist in the state after the given block.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\BlockHash $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetAnonymityRevokers(\Concordium\BlockHash $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetAnonymityRevokers',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get the cryptographic parameters used in the given block.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\BlockHash $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetCryptographicParameters(\Concordium\BlockHash $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetCryptographicParameters',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get a list of all baker IDs registered at that block in ascending order. Or null, if the block is invalid.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\BlockHash $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetBakerList(\Concordium\BlockHash $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetBakerList',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get the status of a pool. If passiveDelegation == true, this returns the status for the passive delegators.
     * Otherwise, it returns the status for the baker with the specified ID (if it exists).
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\GetPoolStatusRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetPoolStatus(\Concordium\GetPoolStatusRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetPoolStatus',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get a list of banned peers.
     * @param \Concordium\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetBannedPeers(\Concordium\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetBannedPeers',
        $argument,
        ['\Concordium\PeerListResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Shut down the node.
     * @param \Concordium\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Shutdown(\Concordium\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/Shutdown',
        $argument,
        ['\Concordium\BoolResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Start dumping packages into the specified file.
     * Only enabled if the node was built with the `network_dump` feature.
     * @param \Concordium\DumpRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DumpStart(\Concordium\DumpRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/DumpStart',
        $argument,
        ['\Concordium\BoolResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Stop dumping packages.
     * Only enabled if the node was built with the `network_dump` feature.
     * @param \Concordium\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DumpStop(\Concordium\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/DumpStop',
        $argument,
        ['\Concordium\BoolResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Query for the status of a transaction by its hash.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\TransactionHash $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetTransactionStatus(\Concordium\TransactionHash $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetTransactionStatus',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Query for transactions in a block by its hash.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\GetTransactionStatusInBlockRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetTransactionStatusInBlock(\Concordium\GetTransactionStatusInBlockRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetTransactionStatusInBlock',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Query for non-finalized transactions present on an account by the account address.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\AccountAddress $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetAccountNonFinalizedTransactions(\Concordium\AccountAddress $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetAccountNonFinalizedTransactions',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Request a summary for a block by its hash.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\BlockHash $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetBlockSummary(\Concordium\BlockHash $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetBlockSummary',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Request next nonce information for an account.
     * A JSON schema for the return type is provided at: https://developer.concordium.software/en/mainnet/net/references/grpc.html.
     * @param \Concordium\AccountAddress $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetNextAccountNonce(\Concordium\AccountAddress $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/concordium.P2P/GetNextAccountNonce',
        $argument,
        ['\Concordium\JsonResponse', 'decode'],
        $metadata, $options);
    }

}
