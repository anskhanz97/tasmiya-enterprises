"""Loopback-only SMTP test mailbox. Captures messages on D:; never forwards mail."""
import argparse
import asyncio
from pathlib import Path

parser = argparse.ArgumentParser()
parser.add_argument('--port', type=int, default=2526)
parser.add_argument('--output', type=Path, required=True)
parser.add_argument('--reject', action='store_true')
args = parser.parse_args()
args.output = args.output.resolve()
if args.output.drive.upper() != 'D:':
    parser.error('Use a D: output directory.')
args.output.mkdir(parents=True, exist_ok=True)
counter = 0


async def handle(reader, writer):
    global counter
    async def reply(message):
        writer.write((message + '\r\n').encode())
        await writer.drain()

    await reply('220 localhost Tasmiya test mailbox')
    try:
        while line := await reader.readline():
            command = line.decode(errors='replace').strip()
            verb = command.split(' ', 1)[0].upper()
            if verb in ('EHLO', 'HELO'):
                await reply('250-localhost\r\n250 SIZE 10485760')
            elif verb in ('MAIL', 'RCPT', 'RSET', 'NOOP'):
                await reply('250 OK')
            elif verb == 'DATA':
                await reply('354 End with a period on its own line')
                data = bytearray()
                while body := await reader.readline():
                    if body == b'.\r\n':
                        break
                    data.extend(body[1:] if body.startswith(b'..') else body)
                counter += 1
                (args.output / f'message-{counter:03d}.eml').write_bytes(data)
                if args.reject:
                    await reply('554 Local test rejection')
                    print(f'rejected message {counter}', flush=True)
                else:
                    await reply('250 Accepted by local test mailbox')
                    print(f'accepted message {counter}', flush=True)
            elif verb == 'QUIT':
                await reply('221 Goodbye')
                break
            else:
                await reply('502 Command not supported by test mailbox')
    finally:
        writer.close()
        await writer.wait_closed()


async def main():
    server = await asyncio.start_server(handle, '127.0.0.1', args.port)
    print(f'listening 127.0.0.1:{args.port}', flush=True)
    async with server:
        await server.serve_forever()


asyncio.run(main())
