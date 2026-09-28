import { PublicClientApplication } from '@azure/msal-browser';

const dialog = document.getElementById('onedrive-picker');

if (dialog) {
    const status = document.getElementById('onedrive-status');
    const list = document.getElementById('onedrive-list');
    const back = document.getElementById('onedrive-back');
    const more = document.getElementById('onedrive-more');
    const client = new PublicClientApplication({
        auth: {
            clientId: dialog.dataset.clientId,
            authority: 'https://login.microsoftonline.com/common',
            redirectUri: `${window.location.origin}/`,
        },
        cache: { cacheLocation: 'sessionStorage' },
    });
    let token;
    let target;
    let folderStack = [];
    let nextUrl;

    const close = () => { dialog.style.display = 'none'; dialog.hidden = true; };
    document.getElementById('onedrive-close').addEventListener('click', close);
    dialog.addEventListener('click', (event) => { if (event.target === dialog) close(); });

    async function graph(url) {
        const response = await fetch(url, { headers: { Authorization: `Bearer ${token}` } });
        if (!response.ok) throw new Error('OneDrive could not load your files. Check the application permissions.');
        return response.json();
    }

    async function loadFolder(folderId = null, append = false) {
        status.textContent = 'Loading images…';
        if (!append) list.replaceChildren();
        const url = append ? nextUrl : `https://graph.microsoft.com/v1.0/me/drive/${folderId ? `items/${encodeURIComponent(folderId)}/children` : 'root/children'}?$select=id,name,file,folder,size&$top=100`;
        const data = await graph(url);
        nextUrl = data['@odata.nextLink'];
        more.style.display = nextUrl ? 'inline-block' : 'none';
        back.style.display = folderStack.length ? 'inline-block' : 'none';
        for (const item of data.value ?? []) {
            if (!item.folder && !item.file?.mimeType?.startsWith('image/')) continue;
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = `${item.folder ? 'Folder: ' : 'Image: '}${item.name}`;
            button.style.cssText = 'text-align:left;border:1px solid #d8d9ed;background:#fafaff;color:#202857;border-radius:9px;padding:12px;cursor:pointer';
            button.addEventListener('click', async () => {
                if (item.folder) {
                    folderStack.push(item.id);
                    await loadFolder(item.id);
                } else {
                    await importImage(item);
                }
            });
            list.appendChild(button);
        }
        status.textContent = list.children.length ? 'Choose an image. Files larger than 5 MB cannot be imported.' : 'No images in this folder.';
    }

    async function importImage(item) {
        try {
            if (item.size > 5 * 1024 * 1024) throw new Error('Choose an image under 5 MB.');
            status.textContent = `Importing ${item.name}…`;
            const metadata = await graph(`https://graph.microsoft.com/v1.0/me/drive/items/${encodeURIComponent(item.id)}`);
            const downloadUrl = metadata['@microsoft.graph.downloadUrl'];
            if (!downloadUrl) throw new Error('OneDrive did not provide a download link for this image.');
            const response = await fetch(downloadUrl);
            if (!response.ok) throw new Error('OneDrive could not download this image.');
            const blob = await response.blob();
            if (!blob.type.startsWith('image/') || blob.size > 5 * 1024 * 1024) throw new Error('Choose a JPG, PNG, GIF or WebP image under 5 MB.');
            const transfer = new DataTransfer();
            transfer.items.add(new File([blob], item.name, { type: blob.type }));
            const input = document.getElementById(`${target}_image`);
            input.files = transfer.files;
            input.dispatchEvent(new Event('change', { bubbles: true }));
            close();
        } catch (error) {
            status.textContent = error.message;
        }
    }

    back.addEventListener('click', async () => {
        folderStack.pop();
        await loadFolder(folderStack.at(-1) ?? null);
    });
    more.addEventListener('click', async () => { if (nextUrl) await loadFolder(folderStack.at(-1) ?? null, true); });

    document.querySelectorAll('.pe-onedrive').forEach(button => button.addEventListener('click', async () => {
        target = button.dataset.target;
        dialog.hidden = false;
        dialog.style.display = 'flex';
        status.textContent = 'Connecting to OneDrive…';
        try {
            await client.initialize();
            const account = client.getAllAccounts()[0];
            const result = account
                ? await client.acquireTokenSilent({ account, scopes: ['Files.Read'] }).catch(() => client.acquireTokenPopup({ scopes: ['Files.Read'] }))
                : await client.loginPopup({ scopes: ['Files.Read'] });
            token = result.accessToken;
            folderStack = [];
            await loadFolder();
        } catch (error) {
            status.textContent = 'OneDrive sign-in was cancelled or could not finish. Check the app registration and try again.';
        }
    }));
}
