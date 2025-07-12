import { BeaconEvent, DAppClient } from "@airgap/beacon-sdk";
import { createMessagePayload, signIn } from "@siwt/sdk";

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
        preferredNetwork: network.type,
    });

    try {
        // request wallet permissions with Beacon dAppClient
        const permissions = await dAppClient.requestPermissions({ network });

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
        const { status, data } = await signIn(dappUrl)({
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
