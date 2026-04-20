/*
 * Copyright (C) 2022, vDL Digital Ventures GmbH <info@vdl.digital>
 *
 * SPDX-License-Identifier: MIT
 */
import { stringToBytes } from "@taquito/utils";
import {
    __,
    addIndex,
    append,
    assoc,
    divide,
    isEmpty,
    isNil,
    join,
    map,
    mapObjIndexed,
    objOf,
    pipe,
    prepend,
    prop,
    reject,
    slice,
    tap,
    unless,
    values,
} from "ramda";

import {
    OPTIONAL_MESSAGE_PROPERTIES,
    SIGN_IN_MESSAGE,
    TEZOS_SIGNED_MESSAGE_PREFIX,
} from "./constants";
import { http } from "./http";

const generateMessageData = (messageData) => {
    const { domain, address } = messageData;

    if (
        !messageData?.nonce &&
        !messageData?.requestId &&
        !messageData?.issuedAt
    ) {
        throw new Error("Invalid message format");
    }

    return pipe(
        mapObjIndexed((value, key) =>
            messageData[key] ? `${value}: ${messageData[key]}` : null,
        ),
        values,
        reject(isNil),
        tap(console.log),
        unless(
            () =>
                isEmpty(messageData?.statement) ||
                isNil(messageData?.statement),
            prepend(`\n${messageData.statement}\n`),
        ),
        prepend(address),
        prepend(`${domain} ${SIGN_IN_MESSAGE}`),
        unless(
            () =>
                isEmpty(messageData?.resources) || isNil(messageData.resources),
            append(
                pipe(
                    addIndex(map)((resource, idx) =>
                        idx === 0
                            ? `Resources:\n- ${resource}`
                            : `- ${resource}`,
                    ),
                    join("\n"),
                )(messageData.resources || []),
            ),
        ),
        reject(isNil),
    )(OPTIONAL_MESSAGE_PROPERTIES);
};

const constructSignPayload = ({ payload, pkh }) => ({
    signingType: "micheline",
    payload,
    sourceAddress: pkh,
});

const calculateLength = pipe(
    prop("length"),
    divide(__, 2),
    (length) => length.toString(16),
    (length) => `00000000${length}`,
    (length) => slice(length.length - 8, length.length)(length),
);

const packMessagePayload = (messageData) =>
    pipe(
        prepend(TEZOS_SIGNED_MESSAGE_PREFIX),
        join("\n"),
        stringToBytes,
        (bytes) => ["05", "01", calculateLength(bytes), bytes],
        join(""),
    )(messageData);

export const signIn = (apiUrl) => (payload) =>
    http(`${apiUrl}/signin`, {
        method: "POST",
        body: JSON.stringify(payload),
    });

export const createMessagePayload = (signatureRequestData) =>
    pipe(
        generateMessageData,
        packMessagePayload,
        objOf("payload"),
        assoc("pkh", prop("address")(signatureRequestData)),
        constructSignPayload,
    )(signatureRequestData);
