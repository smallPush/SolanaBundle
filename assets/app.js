import * as solanaWeb3 from '@solana/web3.js';

document.addEventListener('DOMContentLoaded', () => {
    const signButton = document.getElementById('solana-sign-button');
    if (signButton) {
        signButton.addEventListener('click', async () => {
            signButton.textContent = 'Procesando...';
            signButton.disabled = true;

            const provider = window.solana;
            if (!provider || !provider.isPhantom) {
                alert('Phantom wallet no encontrado. Por favor, instala la extensión de Phantom.');
                signButton.textContent = 'Error: Instala Phantom';
                return;
            }

            try {
                const resp = await provider.connect();
                const connection = new solanaWeb3.Connection(
                    solanaWeb3.clusterApiUrl('devnet'),
                    'confirmed'
                );

                const fromPubkey = new solanaWeb3.PublicKey(resp.publicKey.toString());
                const toPubkey = new solanaWeb3.PublicKey(signButton.dataset.to);
                const lamports = parseFloat(signButton.dataset.amount) * solanaWeb3.LAMPORTS_PER_SOL;

                const transaction = new solanaWeb3.Transaction().add(
                    solanaWeb3.SystemProgram.transfer({
                        fromPubkey: fromPubkey,
                        toPubkey: toPubkey,
                        lamports: lamports,
                    })
                );

                const { blockhash } = await connection.getRecentBlockhash();
                transaction.recentBlockhash = blockhash;
                transaction.feePayer = fromPubkey;

                const { signature } = await provider.signAndSendTransaction(transaction);
                await connection.confirmTransaction(signature, 'confirmed');

                alert('¡Transacción completada con éxito!');
                signButton.textContent = '¡Transacción Enviada!';

                // Here you could redirect or update the UI.
                // A good improvement would be to call back to the server to set the
                // contract status to 'completed'.
                // For now, we just show an alert.

            } catch (err) {
                console.error('Error en la transacción:', err);
                alert(`Error: ${err.message}`);
                signButton.textContent = 'Error en la transacción';
                signButton.disabled = false;
            }
        });
    }
});
