import { BeaconEvent, DAppClient } from "@airgap/beacon-sdk";
import { createMessagePayload, signIn } from "@siwt/sdk";

export const connect = async (dappUrl, csrfToken) => {
    const domain = new URL(dappUrl).hostname;
    const dAppClient = new DAppClient({ name: domain });

    // request wallet permissions with Beacon dAppClient
    const permissions = await dAppClient.requestPermissions();

    // create the message to be signed
    const messagePayload = createMessagePayload({
        domain: domain,
        address: permissions.address,
        uri: dappUrl,
        version: "1",
        chainId: "NetXdQprcVkpaWU", // NetXnHfVqm9iesp for ghostnet
        statement: "I accept the SIWT Terms of Service: https://siwt.xyz/tos",
        nonce: "32891756",
        issuedAt: "2024-03-05T16:25:24Z",
        resources: [
            "ipfs://bafybeiemxf5abjwjbikoz4mc3a3dla6ual3jsgpdr4cjr3oz3evfyavhwq/",
            "https://siwt.xyz/privacy-policy",
        ],
    });

    // request the signature
    const signedPayload = await dAppClient.requestSignPayload(messagePayload);

    // sign in the user to our app
    const { status, data } = await signIn(dappUrl)({
        pk: permissions.accountInfo?.publicKey || "",
        pkh: permissions.address,
        message: messagePayload.payload,
        signature: signedPayload.signature,
        _csrfToken: csrfToken,
    });

    dAppClient.destroy();
    window.location.href = dappUrl;
};
