import { TezosToolkit } from "@taquito/taquito";
import { Tzip16Module, tzip16 } from "@taquito/tzip16";
import { DAppClient } from "@tezos-x/octez.connect-sdk";
import { http } from "./http";
import { createMessagePayload, signIn } from "./utils";

export const connect = async (
    network,
    dappUrl,
    csrfToken,
    chainId,
    statement,
    nonce,
    issuedAt,
) => {
    const domain = new URL(dappUrl).hostname;
    const dAppClient = new DAppClient({
        name: domain,
        enableMetrics: false,
        network,
    });

    try {
        // request wallet permissions with Beacon dAppClient
        const permissions = await dAppClient.requestPermissions();

        // create the message to be signed
        const messagePayload = createMessagePayload({
            domain,
            address: permissions.address,
            uri: dappUrl,
            version: "1",
            chainId,
            statement,
            nonce,
            issuedAt,
        });

        // request the signature
        const signedPayload =
            await dAppClient.requestSignPayload(messagePayload);

        // sign in the user to our app
        await signIn(dappUrl)({
            pk: permissions.accountInfo?.publicKey || "",
            pkh: permissions.address,
            message: messagePayload.payload,
            signature: signedPayload.signature,
            _csrfToken: csrfToken,
        });

        dAppClient.destroy();
        window.location.href = "/";
    } catch (e) {
        console.log(e);
    }
};

export const getMetadata = async (address, callBackUrl, csrfToken) => {
    const Tezos = new TezosToolkit("http://localhost:8732");
    Tezos.addExtension(new Tzip16Module());

    const contract = await Tezos.contract.at(address, tzip16);
    const metadata = await contract.tzip16().getMetadata();
    // const views = await contract.tzip16().metadataViews();

    return http(callBackUrl, {
        method: "POST",
        body: JSON.stringify({
            _csrfToken: csrfToken,
            metadata: JSON.stringify(metadata.metadata),
        }),
    });
};
