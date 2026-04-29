/*
 * Copyright (C) 2022, vDL Digital Ventures GmbH <info@vdl.digital>
 *
 * SPDX-License-Identifier: MIT
 */

const fetchWithTimeout = async (resource, userOptions) => {
    const defaultOptions = {
        timeout: 3000,
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
    };
    const options = { ...defaultOptions, ...userOptions };

    const controller = new AbortController();
    const id = setTimeout(() => controller.abort(), options.timeout);

    try {
        const response = await fetch(resource, {
            ...options,
            signal: controller.signal,
        });
        const data = await response.json();
        return { data };
    } catch (_error) {
        throw new Error("Fetching failed");
    } finally {
        clearTimeout(id);
    }
};

export const http = fetchWithTimeout;
