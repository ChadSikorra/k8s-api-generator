<?php

/**
 * This file is part of the k8s/api-generator library.
 *
 * (c) Chad Sikorra <Chad.Sikorra@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace K8s\ApiGenerator\Code;

use K8s\ApiGenerator\Code\CodeGenerator\ModelCodeGenerator;
use K8s\ApiGenerator\Code\CodeGenerator\ServiceCodeGenerator;
use K8s\ApiGenerator\Code\CodeGenerator\ServiceFactoryCodeGenerator;
use K8s\ApiGenerator\Code\Writer\PhpFileWriter;
use K8s\ApiGenerator\Parser\Metadata\Metadata;

class CodeGenerator
{
    private ModelCodeGenerator $modelCodeGenerator;

    private ServiceCodeGenerator $serviceCodeGenerator;

    private ServiceFactoryCodeGenerator $serviceFactoryCodeGenerator;

    private PhpFileWriter $phpFileWriter;

    public function __construct(
        ?PhpFileWriter $phpFileWriter = null,
        ?ServiceCodeGenerator $serviceCodeGenerator = null,
        ?ModelCodeGenerator $modelCodeGenerator = null,
        ?ServiceFactoryCodeGenerator $serviceFactoryCodeGenerator = null
    ) {
        $this->phpFileWriter = $phpFileWriter ?? new PhpFileWriter();
        $this->modelCodeGenerator = $modelCodeGenerator ?? new ModelCodeGenerator();
        $this->serviceCodeGenerator = $serviceCodeGenerator ?? new ServiceCodeGenerator();
        $this->serviceFactoryCodeGenerator = $serviceFactoryCodeGenerator ?? new ServiceFactoryCodeGenerator();
    }

    public function generateCode(Metadata $metadata, CodeOptions $options): void
    {
        foreach ($metadata->getDefinitions() as $model) {
            if ($model->isValidModel()) {
                $codeFile = $this->modelCodeGenerator->generate($model, $metadata, $options);
                $this->phpFileWriter->write(
                    $codeFile,
                    $options
                );
            }
        }
        foreach ($metadata->getServiceGroups() as $serviceGroup) {
            $codeFile = $this->serviceCodeGenerator->generate(
                $serviceGroup,
                $metadata,
                $options
            );
            $this->phpFileWriter->write(
                $codeFile,
                $options
            );
        }
        $codeFile = $this->serviceFactoryCodeGenerator->generate(
            $metadata,
            $options
        );
        $this->phpFileWriter->write(
            $codeFile,
            $options
        );
    }
}
