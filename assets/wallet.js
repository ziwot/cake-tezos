import { TezosToolkit } from "@taquito/taquito";
import { BeaconEvent, DAppClient } from "@tezos-x/octez.connect-sdk";

const clients = new Map();

const clientKey = (name, network) => JSON.stringify({ name, network });

/**
 * Returns the shared DAppClient for a wallet name and network.
 */
export const getDAppClient = (network, name = window.location.hostname) => {
    const key = clientKey(name, network);

    if (!clients.has(key)) {
        const client = new DAppClient({ name, network });

        // The SDK warns when an account is restored before anything subscribes
        // to ACTIVE_ACCOUNT_SET. Subscribing here also marks the active account
        // as handled for the SIWT connect and later contract calls.
        client
            .subscribeToEvent(BeaconEvent.ACTIVE_ACCOUNT_SET, () => { })
            .catch(() => { });

        clients.set(key, client);
    }

    return clients.get(key);
};

/**
 * Returns the active wallet account, requesting permissions if needed.
 */
export const getWallet = async (network, name = window.location.hostname) => {
    const client = getDAppClient(network, name);
    let account = await client.getActiveAccount();

    if (!account) {
        await client.requestPermissions({});
        account = await client.getActiveAccount();
    }

    if (!account) {
        throw new Error("No active wallet account.");
    }

    return { client, account };
};

/**
 * Sends a raw operation through the wallet.
 */
export const requestOperation = async (
    network,
    operationDetails,
    name = window.location.hostname,
) => {
    const { client } = await getWallet(network, name);

    return client.requestOperation({ operationDetails });
};

/**
 * Encodes a contract call with Taquito and sends it through the wallet.
 *
 * `args` can be an object for named/annotated parameters or an array for
 * positional parameters. Returns the operation hash once the wallet has
 * broadcast it; it does not wait for confirmation.
 */
export const callContract = async ({
    network,
    contract,
    entrypoint = "default",
    args,
    amount = "0",
    sourceAddress,
    name = window.location.hostname,
}) => {
    const { client, account } = await getWallet(network, name);

    if (sourceAddress && account.address !== sourceAddress) {
        throw new Error(
            "The connected wallet does not match the expected address.",
        );
    }

    const tezos = new TezosToolkit(network.rpcUrl);
    const abstraction = await tezos.contract.at(contract);
    const methodObject = abstraction.methodsObject?.[entrypoint];
    const method = methodObject ?? abstraction.methods?.[entrypoint];

    if (!method) {
        throw new Error(`Entrypoint "${entrypoint}" not found on ${contract}.`);
    }

    const call = methodObject
        ? Array.isArray(args)
            ? methodObject(...args)
            : methodObject(args)
        : method(...(Array.isArray(args) ? args : [args]));
    const params = call.toTransferParams({ amount, mutez: true });
    const operation = {
        kind: "transaction",
        destination: params.to,
        amount: String(params.amount ?? 0),
    };

    if (params.parameter) {
        operation.parameters = params.parameter;
    }

    const response = await client.requestOperation({
        operationDetails: [operation],
    });

    return { hash: response.transactionHash, account };
};
