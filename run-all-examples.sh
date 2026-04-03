#!/bin/bash

echo "=== Running All Langfuse Examples with Delays ==="
echo ""

examples=(
    "example:basic-agent"
    "example:agent-with-tools"
    "example:structured-output"
    "example:streaming"
    "example:prompt-management"
    "example:scoring"
    "example:rag-pipeline"
    "example:multi-agent"
    "example:conversation"
)

total=${#examples[@]}
current=0

for example in "${examples[@]}"; do
    current=$((current + 1))
    echo "[$current/$total] Running: php artisan $example"
    echo "---"
    
    php artisan "$example"
    exit_code=$?
    
    if [ $exit_code -eq 0 ]; then
        echo "✅ SUCCESS"
    else
        echo "❌ FAILED (exit code: $exit_code)"
    fi
    
    echo ""
    
    # Delay between examples (except after the last one)
    if [ $current -lt $total ]; then
        echo "Waiting 15 seconds before next example..."
        sleep 15
        echo ""
    fi
done

echo ""
echo "=== All Examples Completed ==="
echo "Check Langfuse UI: http://localhost:3000"
